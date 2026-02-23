<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserActionSetting;
use Illuminate\Http\Request;

class ActionSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $actionSettings = auth()->user()->actionSettings()->with('platformAction.platform')->get();
        return view('user.actions.index', compact('actionSettings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $platformActions = \App\Models\PlatformAction::with('platform')->get();
        return view('user.actions.create', compact('platformActions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform_action_id' => 'required|exists:platform_actions,id',
            'target_time' => 'required|date_format:H:i',
        ]);

        $validated['is_active'] = $request->has('is_active');

        auth()->user()->actionSettings()->create($validated);

        return redirect()->route('user.actions.index')->with('success', 'Automated action scheduled successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserActionSetting $action)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserActionSetting $action)
    {
        $platformActions = \App\Models\PlatformAction::with('platform')->get();
        return view('user.actions.edit', compact('action', 'platformActions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserActionSetting $action)
    {
        $validated = $request->validate([
            'platform_action_id' => 'required|exists:platform_actions,id',
            'target_time' => 'required|date_format:H:i',
        ]);

        $carbonTime = \Carbon\Carbon::parse($validated['target_time']);
        $validated['target_time'] = $carbonTime->format('H:i:s'); // Ensure it saves as valid time object
        $validated['is_active'] = $request->has('is_active');

        $action->update($validated);

        return redirect()->route('user.actions.index')->with('success', 'Automated action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserActionSetting $action)
    {
        $action->delete();
        return redirect()->route('user.actions.index')->with('success', 'Automated action deleted successfully.');
    }
}
