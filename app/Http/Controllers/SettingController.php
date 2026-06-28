<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use App\Support\TableQuery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canDo('settings.view'), 403);

        $query = Setting::query()
            ->when($request->string('search')->toString(), fn ($query, string $term) => $query->where(function ($query) use ($term): void {
                $query->where('key', 'like', "%{$term}%")
                    ->orWhere('group', 'like', "%{$term}%")
                    ->orWhere('value', 'like', "%{$term}%");
            }));

        TableQuery::applySort($query, $request, [
            'group' => 'group',
            'key' => 'key',
            'value' => 'value',
            'updated_at' => 'updated_at',
        ], 'group', 'asc');

        return Inertia::render('settings/Index', [
            'settings' => $query->get(),
            'filters' => TableQuery::filters($request, ['search', 'sort', 'direction']),
        ]);
    }

    public function update(UpdateSettingRequest $request, SettingService $settings): RedirectResponse
    {
        abort_unless($request->user()?->canDo('settings.update'), 403);

        foreach ($request->validated('settings') as $key => $value) {
            $settings->set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
