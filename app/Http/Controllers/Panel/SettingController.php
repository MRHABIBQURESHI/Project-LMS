<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\StoreFileTrait;

class SettingsController extends Controller
{
    use StoreFileTrait;

    /**
     * Display the settings edit form.
     */
    public function index()
    {
        $settings = Setting::first();
        return view('panel.settings', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'phone_number' => ['nullable', 'array'],
            'phone_number.*' => ['nullable', 'string', 'max:20'],
            'email_address' => ['nullable', 'array'],
            'email_address.*' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'youtube' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'working_hours' => ['nullable', 'string'],
            'footer_description' => ['nullable', 'string'],
            'logo_black' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_white' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'fav_icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,ico', 'max:1024'],
        ]);

        DB::beginTransaction();

        try {
            $settings = Setting::firstOrNew();
            // Fill scalar fields
            $settings->fill([
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
                'address' => $validated['address'] ?? null,
                'facebook' => $validated['facebook'] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'youtube' => $validated['youtube'] ?? null,
                'twitter' => $validated['twitter'] ?? null,
                'linkedin' => $validated['linkedin'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'working_hours' => $validated['working_hours'] ?? null,
                'footer_description' => $validated['footer_description'] ?? null,
            ]);

            // Handle JSON fields – encode arrays
            $settings->phone_number = isset($validated['phone_number']) ? json_encode($validated['phone_number']) : null;
            $settings->email_address = isset($validated['email_address']) ? json_encode($validated['email_address']) : null;

            // Handle image uploads (replace old files)
            if ($request->hasFile('logo_black')) {
                $settings->logo_black = $this->storeFile($request->file('logo_black'), 'settings', $settings->logo_black);
            }
            if ($request->hasFile('logo_white')) {
                $settings->logo_white = $this->storeFile($request->file('logo_white'), 'settings', $settings->logo_white);
            }
            if ($request->hasFile('fav_icon')) {
                $settings->fav_icon = $this->storeFile($request->file('fav_icon'), 'settings', $settings->fav_icon);
            }

            // Set updated_by to current user
            $settings->updated_by = $request->user()->id;

            $settings->save();
            DB::commit();

            return redirect()->route('panel.settings.index')
                ->with('success', 'Settings updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SettingsController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating settings.');
        }
    }
}