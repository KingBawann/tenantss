<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $settings = $tenant->settings ?? [];

        return view('settings.index', compact('tenant', 'settings'));
    }

    public function update(Request $request)
    {
        $tenant = auth()->user()->tenant;
        
        $validated = $request->validate([
            'settings.store_name' => 'nullable|string|max:255',
            'settings.store_phone' => 'nullable|string|max:255',
            'settings.store_address' => 'nullable|string',
            'settings.receipt_header' => 'nullable|string',
            'settings.receipt_footer' => 'nullable|string',
            'settings.receipt_paper_size' => 'nullable|in:80mm,a4',
            'settings.tax_rate' => 'nullable|numeric|min:0|max:100',
            'settings.currency_symbol' => 'nullable|string|max:10',
        ]);

        // Merge existing settings with new ones
        $currentSettings = $tenant->settings ?? [];
        $newSettings = array_merge($currentSettings, $validated['settings'] ?? []);

        $tenant->settings = $newSettings;
        $tenant->save();

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
