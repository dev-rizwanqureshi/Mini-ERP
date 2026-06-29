<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\TableQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canDo('products.viewAny') || $request->user()?->canDo('products.view'), 403);

        $query = Product::query()
            ->search($request->string('search')->toString())
            ->when($request->string('unit')->toString(), fn (Builder $query, string $unit) => $query->where('unit', $unit));

        $this->applyStockFilter($query, $request);

        TableQuery::applySort($query, $request, [
            'name' => 'name',
            'sku' => 'sku',
            'stock_quantity' => 'stock_quantity',
            'low_stock_threshold' => 'low_stock_threshold',
            'unit' => 'unit',
            'updated_at' => 'updated_at',
        ], 'stock_quantity', 'asc');

        return Inertia::render('Stock/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => TableQuery::filters($request, ['search', 'unit', 'stock_filter', 'threshold', 'sort', 'direction']),
            'units' => Product::query()->select('unit')->distinct()->orderBy('unit')->pluck('unit')->filter()->values(),
            'stats' => [
                'total' => Product::query()->count(),
                'out' => Product::query()->where('stock_quantity', '<=', 0)->count(),
                'low' => Product::query()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0)->count(),
                'soon' => Product::query()->whereColumn('stock_quantity', '>', 'low_stock_threshold')->whereRaw('stock_quantity <= low_stock_threshold * 2')->count(),
            ],
        ]);
    }

    private function applyStockFilter(Builder $query, Request $request): void
    {
        match ($request->string('stock_filter')->toString()) {
            'out' => $query->where('stock_quantity', '<=', 0),
            'low' => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0),
            'soon' => $query->whereColumn('stock_quantity', '>', 'low_stock_threshold')->whereRaw('stock_quantity <= low_stock_threshold * 2'),
            'below_5' => $query->where('stock_quantity', '<', 5),
            'below_10' => $query->where('stock_quantity', '<', 10),
            'below_custom' => $query->where('stock_quantity', '<', max(0, (float) $request->input('threshold', 0))),
            default => null,
        };
    }
}
