<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Settings/Index', ['settings' => Setting::query()->orderBy('group')->orderBy('key')->get()]);
    }

    public function update(UpdateSettingRequest $request, SettingService $settings): RedirectResponse
    {
        foreach ($request->validated('settings') as $key => $value) {
            $settings->set($key, $value);
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
