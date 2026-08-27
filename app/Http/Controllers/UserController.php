<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('users.index', compact('users'));
    }
    
    public function create()
    {
        $outlets = \App\Models\Outlet::where('is_active', true)->get();
        return view('users.create', compact('outlets'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,employee',
            'outlet_id' => 'nullable|exists:outlets,id'
        ]);
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'outlet_id' => $validated['outlet_id'] ?? null
        ]);
        
        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }
    
    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot edit your own account here. Use Profile settings.');
        }
        
        $outlets = \App\Models\Outlet::where('is_active', true)->get();
        return view('users.edit', compact('user', 'outlets'));
    }
    
    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot edit your own account.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,employee',
            'outlet_id' => 'nullable|exists:outlets,id'
        ]);
        
        $user->update($validated);
        
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
    
    public function resetPassword(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Reset your password via Profile settings.');
        }
        
        return view('users.reset-password', compact('user'));
    }
    
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return redirect()->route('users.index')
            ->with('success', 'Password reset successfully!');
    }
}