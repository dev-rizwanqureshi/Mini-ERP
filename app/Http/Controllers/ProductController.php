<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Http\Requests\Product\AdjustStockRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\StockService;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->search($request->string('search')->toString());

        TableQuery::applySort($query, $request, [
            'name' => 'name',
            'sku' => 'sku',
            'unit_price' => 'unit_price',
            'stock_quantity' => 'stock_quantity',
            'created_at' => 'created_at',
        ]);

        return Inertia::render('Products/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Products/Create', ['statuses' => ProductStatus::cases()]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->authorize('create', Product::class);

        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $openingStock = (float) $data['stock_quantity'];
            $data['stock_quantity'] = 0;

            $product = Product::query()->create([...$data, 'created_by' => $request->user()->id]);

            if ($openingStock > 0) {
                app(StockService::class)->receive($product, $openingStock, $request->user(), 'Opening stock');
            }
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        return Inertia::render('Products/Show', ['product' => $product->load(['stockMovements.user'])]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Products/Edit', ['product' => $product, 'statuses' => ProductStatus::cases()]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        DB::transaction(function () use ($request, $product): void {
            $data = $request->validated();
            $targetStock = (float) $data['stock_quantity'];
            unset($data['stock_quantity']);

            $product->update($data);
            app(StockService::class)->correctTo($product, $targetStock, $request->user(), 'Stock corrected from product edit');
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function adjustStock(AdjustStockRequest $request, Product $product, StockService $stock): RedirectResponse
    {
        $this->authorize('adjustStock', $product);

        $data = $request->validated();
        $quantity = (float) $data['quantity'];
        $notes = $data['notes'] ?: match ($data['mode']) {
            'add' => 'Manual stock received',
            'remove' => 'Manual stock removal',
            default => 'Manual stock correction',
        };

        match ($data['mode']) {
            'add' => $stock->receive($product, $quantity, $request->user(), $notes),
            'remove' => $stock->remove($product, $quantity, $request->user(), $notes),
            'set' => $stock->correctTo($product, $quantity, $request->user(), $notes),
        };

        return back()->with('success', 'Stock updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
