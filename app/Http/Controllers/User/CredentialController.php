<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserPlatformCredential;
use Illuminate\Http\Request;

class CredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $credentials = auth()->user()->credentials()->with('platform')->get();
        return view('user.credentials.index', compact('credentials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userPlatformIds = auth()->user()->credentials()->pluck('platform_id');
        $platforms = \App\Models\Platform::whereNotIn('id', $userPlatformIds)->get();
        return view('user.credentials.create', compact('platforms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform_id' => [
                'required',
                'exists:platforms,id',
                \Illuminate\Validation\Rule::unique('user_platform_credentials')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'location' => 'nullable|string|max:255',
        ]);

        auth()->user()->credentials()->create($validated);

        return redirect()->route('user.credentials.index')->with('success', 'Platform credentials added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPlatformCredential $credential)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserPlatformCredential $credential)
    {
        // Users should be able to see their own currently assigned platform, so we don't exclude it here.
        $platforms = \App\Models\Platform::all();
        return view('user.credentials.edit', compact('credential', 'platforms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserPlatformCredential $credential)
    {
        $validated = $request->validate([
            'platform_id' => [
                'required',
                'exists:platforms,id',
                \Illuminate\Validation\Rule::unique('user_platform_credentials')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($credential->id),
            ],
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'location' => 'nullable|string|max:255',
        ]);

        $credential->update($validated);

        return redirect()->route('user.credentials.index')->with('success', 'Platform credentials updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPlatformCredential $credential)
    {
        $credential->delete();
        return redirect()->route('user.credentials.index')->with('success', 'Platform credentials deleted successfully.');
    }
}
