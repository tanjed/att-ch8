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
            'buffer_minutes' => 'nullable|integer|min:0',
            'weekly_off_days' => 'nullable|array',
            'weekly_off_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ]);

        $carbonTime = \Carbon\Carbon::parse($validated['target_time']);
        $validated['target_time'] = $carbonTime->format('H:i:s'); // Ensure it saves as valid time object
        $validated['is_active'] = $request->has('is_active');
        $validated['weekly_off_days'] = $request->input('weekly_off_days', null);
        $buffer = $validated['buffer_minutes'] ?? 0;

        // Calculate the initial next_execution_time based on buffer
        if ($buffer > 0) {
            $randomOffset = rand(-$buffer, $buffer);
            $validated['next_execution_time'] = $carbonTime->addMinutes($randomOffset)->format('H:i:s');
        } else {
            $validated['next_execution_time'] = $validated['target_time'];
        }

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
            'buffer_minutes' => 'nullable|integer|min:0',
            'weekly_off_days' => 'nullable|array',
            'weekly_off_days.*' => 'string|in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
        ]);

        $carbonTime = \Carbon\Carbon::parse($validated['target_time']);
        $validated['target_time'] = $carbonTime->format('H:i:s'); // Ensure it saves as valid time object
        $validated['is_active'] = $request->has('is_active');
        $validated['weekly_off_days'] = $request->input('weekly_off_days', null); // Clears if empty/null
        $buffer = $validated['buffer_minutes'] ?? 0;

        // Calculate a new next_execution_time based on the updated settings
        if ($buffer > 0) {
            $randomOffset = rand(-$buffer, $buffer);
            $validated['next_execution_time'] = $carbonTime->copy()->addMinutes($randomOffset)->format('H:i:s');
        } else {
            $validated['next_execution_time'] = $validated['target_time'];
        }

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
