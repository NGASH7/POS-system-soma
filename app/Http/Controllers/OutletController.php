<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function index()
    {
        $outlets = Outlet::withCount(['users', 'products', 'sales'])->orderBy('id')->paginate(10);
        return view('admin.outlets.index', compact('outlets'));
    }

    public function create()
    {
        return view('admin.outlets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Outlet::create($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Branch created successfully!');
    }

    public function edit(Outlet $outlet)
    {
        return view('admin.outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_info' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $outlet->update($validated);

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Branch updated successfully!');
    }

    public function destroy(Outlet $outlet)
    {
        // 1. Prevent deleting default outlet (ID = 1)
        if ($outlet->id === 1) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete the Main Outlet (Default HQ).');
        }

        // 2. Prevent deleting the only remaining outlet
        if (Outlet::count() <= 1) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete the only remaining outlet. At least one outlet must exist.');
        }

        // 3. Prevent deleting currently active outlet in session
        if ($outlet->id == session('active_outlet_id')) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete the outlet you are currently switched to. Switch to another outlet first.');
        }

        // 4. Prevent deleting outlet if it has associated active users
        if ($outlet->users()->count() > 0) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete outlet with assigned users. Reassign or delete users first.');
        }

        // 5. Prevent deleting outlet if it has associated products
        if ($outlet->products()->count() > 0) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete outlet with associated products. Reassign or delete products first.');
        }

        // 6. Prevent deleting outlet if it has historical sales
        if ($outlet->sales()->count() > 0) {
            return redirect()->route('admin.outlets.index')
                ->with('error', 'Cannot delete outlet with historical sales. You can deactivate the outlet instead.');
        }

        $outlet->delete();

        return redirect()->route('admin.outlets.index')
            ->with('success', 'Branch deleted successfully!');
    }
}
