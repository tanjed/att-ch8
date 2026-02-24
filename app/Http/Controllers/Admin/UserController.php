<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        // Don't let users downgrade themselves
        if ($user->id === auth()->id() && $request->role !== 'super_admin') {
            return back()->with('error', 'You cannot change your own super_admin role.');
        }

        $validated = $request->validate([
            'role' => 'required|in:super_admin,Manager,CONSUMER',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User role updated successfully.');
    }
}
