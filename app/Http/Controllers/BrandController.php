<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::withCount('products')
            ->where('outlet_id', Auth::user()->outlet_id) // Filter by outlet
            ->orderBy('name')
            ->paginate(20);
            
        return view('products.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('products.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string|max:1000',
            'website'     => 'nullable|url|max:255',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');
        $validated['outlet_id'] = Auth::user()->outlet_id; // Auto-assign outlet

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($validated);

        return redirect()->route('products.brands')
            ->with('success', 'Brand created successfully!');
    }

    public function edit(Brand $brand)
    {
        // Ensure brand belongs to user's outlet
        if ($brand->outlet_id !== Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access to this brand.');
        }
        
        return view('products.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        // Ensure brand belongs to user's outlet
        if ($brand->outlet_id !== Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access to this brand.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string|max:1000',
            'website'     => 'nullable|url|max:255',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($validated);

        return redirect()->route('products.brands')
            ->with('success', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        // Ensure brand belongs to user's outlet
        if ($brand->outlet_id !== Auth::user()->outlet_id) {
            abort(403, 'Unauthorized access to this brand.');
        }

        if ($brand->products()->count() > 0) {
            return redirect()->route('products.brands')
                ->with('error', 'Cannot delete a brand that has products assigned to it. Reassign the products first.');
        }

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return redirect()->route('products.brands')
            ->with('success', 'Brand deleted successfully!');
    }
}