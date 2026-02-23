<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformAction;
use Illuminate\Http\Request;

class PlatformActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actions = PlatformAction::with('platform')->get();
        return view('admin.actions.index', compact('actions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $platforms = \App\Models\Platform::all();
        return view('admin.actions.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|string|max:255',
            'api_curl_template' => 'required|string',
        ]);

        PlatformAction::create($validated);

        return redirect()->route('admin.actions.index')->with('success', 'Action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PlatformAction $action)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlatformAction $action)
    {
        $platformAction = $action;
        $platforms = \App\Models\Platform::all();
        return view('admin.actions.edit', compact('platformAction', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PlatformAction $action)
    {
        $validated = $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'name' => 'required|string|max:255',
            'api_curl_template' => 'required|string',
        ]);

        $action->update($validated);

        return redirect()->route('admin.actions.index')->with('success', 'Action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlatformAction $action)
    {
        $action->delete();
        return redirect()->route('admin.actions.index')->with('success', 'Action deleted successfully.');
    }
}
