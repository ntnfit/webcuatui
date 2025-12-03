<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'active');

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'search' => $request->input('search', ''),
            'sort' => $sort,
        ]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedProducts = Product::where('status', 'active')
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function cart()
    {
        return view('shop.cart');
    }

    public function getCartItems(Request $request)
    {
        $cartIds = $request->input('ids', []);
        
        if (empty($cartIds)) {
            return response()->json([]);
        }

        $products = Product::whereIn('id', $cartIds)
            ->where('status', 'active')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->sale_price ?? $product->price,
                    'original_price' => $product->price,
                    'image' => $product->image,
                    'quantity' => $product->quantity,
                ];
            });

        return response()->json($products);
    }

    public function checkout()
    {
        return view('shop.checkout');
    }

    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:255',
            'shipping_address' => 'required|string',
        ]);

        // Get cart from request
        $cart = $request->input('cart', []);
        
        if (empty($cart)) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Calculate total
        $productIds = array_column($cart, 'id');
        $products = Product::whereIn('id', $productIds)->get();
        
        $total = 0;
        foreach ($cart as $item) {
            $product = $products->firstWhere('id', $item['id']);
            if ($product) {
                $price = $product->sale_price ?? $product->price;
                $total += $price * $item['quantity'];
            }
        }

        // Create order
        $order = \App\Models\Order::create([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'shipping_address' => $validated['shipping_address'],
            'total_amount' => $total,
            'status' => 'pending',
            'payment_method' => 'cod',
        ]);

        // Create order details
        foreach ($cart as $item) {
            $product = $products->firstWhere('id', $item['id']);
            if ($product) {
                $price = $product->sale_price ?? $product->price;
                \App\Models\OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'line_total' => $price * $item['quantity'],
                ]);
            }
        }

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }
}
