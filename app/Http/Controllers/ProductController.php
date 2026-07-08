<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->paginate(20);
        return view('products.index', compact('products'));
    }
    
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }
    
    public function store(Request $request)
    {
        // Check if we're updating an existing product (adding stock)
        if ($request->has('existing_product_id') && !empty($request->existing_product_id)) {
            $product = Product::findOrFail($request->existing_product_id);
            
            $validated = $request->validate([
                'stock_quantity' => 'required|integer|min:0',
                'low_stock_threshold' => 'nullable|integer|min:0',
            ]);
            
            // Add stock to existing product
            $newStock = $product->stock_quantity + $validated['stock_quantity'];
            $product->update([
                'stock_quantity' => $newStock,
                'low_stock_threshold' => $validated['low_stock_threshold'] ?? $product->low_stock_threshold
            ]);
            
            return redirect()->route('products.index')
                ->with('success', "Added {$validated['stock_quantity']} items to {$product->name}. New stock: {$newStock}");
        }
        
        // Create new product
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'barcode' => 'nullable|string|unique:products',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        Product::create($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully!');
    }
    
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }
    
    /**
     * Search for products (for AJAX search)
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        
        if (empty($search) || strlen($search) < 2) {
            return response()->json([]);
        }
        
        $products = Product::with('category')
            ->where('is_active', true)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
        
        return response()->json($products);
    }
    
    /**
     * Get product details for AJAX (for quick stock addition)
     */
    public function getProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return response()->json($product);
    }
    
    /**
     * Add stock to existing product (standalone endpoint)
     */
    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $newStock = $product->stock_quantity + $request->quantity;
        
        $product->update([
            'stock_quantity' => $newStock
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Added {$request->quantity} items to {$product->name}",
            'new_stock' => $newStock
        ]);
    }
    
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $product->update($validated);
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully!');
    }
    
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully!');
    }
    
    public function importForm()
    {
        return view('products.import');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:5120'
        ]);
        
        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return redirect()->route('products.index')
                ->with('success', 'Products imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing products: ' . $e->getMessage());
        }
    }
    
    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
    
    /**
     * Display the specified product.
     * Redirect to edit since we don't need a separate show page.
     */
    public function show($id)
    {
        return redirect()->route('products.edit', $id);
    }
}