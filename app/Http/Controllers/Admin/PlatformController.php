<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $platforms = Platform::all();
        return view('admin.platforms.index', compact('platforms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.platforms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'authentication_curl_template' => 'nullable|string',
            'auth_token_key' => 'nullable|string|max:255',
            'refresh_curl_template' => 'nullable|string',
            'refresh_token_key' => 'nullable|string|max:255',
            'related_auth_curl' => 'nullable|string',
        ]);

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('platforms', 'public');
            $validated['icon'] = '/storage/' . $path;
        }

        Platform::create($validated);

        return redirect()->route('admin.platforms.index')->with('success', 'Platform created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Platform $platform)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Platform $platform)
    {
        return view('admin.platforms.edit', compact('platform'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Platform $platform)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'authentication_curl_template' => 'nullable|string',
            'auth_token_key' => 'nullable|string|max:255',
            'refresh_curl_template' => 'nullable|string',
            'refresh_token_key' => 'nullable|string|max:255',
            'related_auth_curl' => 'nullable|string',
        ]);

        if ($request->hasFile('icon')) {
            // Optionally delete old icon if it exists and is an uploaded file
            if ($platform->icon && str_starts_with($platform->icon, '/storage/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('/storage/', '', $platform->icon));
            }
            $path = $request->file('icon')->store('platforms', 'public');
            $validated['icon'] = '/storage/' . $path;
        } else {
            // Keep existing icon if no new file uploaded
            unset($validated['icon']);
        }

        $platform->update($validated);

        return redirect()->route('admin.platforms.index')->with('success', 'Platform updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Platform $platform)
    {
        $platform->delete();
        return redirect()->route('admin.platforms.index')->with('success', 'Platform deleted successfully.');
    }
}
