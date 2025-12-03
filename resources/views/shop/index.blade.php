@extends('layouts.main')

@section('title', 'Shop | My Portfolio')
@section('description', 'Browse our products')

@section('content')
    @include('partials.navbar')
    
    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="pt-16">
            <!-- Hero Section -->
            <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-12">
                <div class="max-w-7xl mx-auto px-4">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">Shop</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Discover our amazing products</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Search & Sort Bar -->
                <div class="mb-8 flex flex-col md:flex-row gap-4 items-center justify-between">
                    <!-- Search -->
                    <form method="GET" action="{{ route('shop.index') }}" class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" 
                                name="search" 
                                value="{{ $search }}"
                                placeholder="Search products..." 
                                class="w-full px-4 py-3 pl-12 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </form>

                    <!-- Sort -->
                    <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-2">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <label class="text-gray-700 dark:text-gray-300 font-medium">Sort by:</label>
                        <select name="sort" 
                            onchange="this.form.submit()"
                            class="px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </form>
                </div>

                <!-- Products Grid -->
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                        @foreach($products as $product)
                            <div class="group">
                                <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 dark:border-gray-700">
                                    <!-- Product Image -->
                                    <a href="{{ route('shop.show', $product->slug) }}" class="block relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                alt="{{ $product->name }}" 
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                loading="lazy">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-20 h-20 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        
                                        <!-- Sale Badge -->
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                                -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                            </div>
                                        @endif

                                        <!-- Stock Badge -->
                                        @if($product->quantity <= 0)
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                                <span class="text-white text-lg font-bold">Out of Stock</span>
                                            </div>
                                        @endif
                                    </a>

                                    <!-- Product Info -->
                                    <div class="p-4">
                                        <a href="{{ route('shop.show', $product->slug) }}">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $product->name }}
                                            </h3>
                                        </a>
                                        
                                        <div class="flex items-center gap-2 mb-3">
                                            @if($product->sale_price && $product->sale_price < $product->price)
                                                <span class="text-xl font-bold text-red-600 dark:text-red-400">
                                                    {{ number_format($product->sale_price, 0, ',', '.') }}₫
                                                </span>
                                                <span class="text-sm text-gray-500 line-through">
                                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                                </span>
                                            @else
                                                <span class="text-xl font-bold text-gray-900 dark:text-white">
                                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                                </span>
                                            @endif
                                        </div>

                                        @if($product->quantity > 0 && $product->quantity <= 10)
                                            <p class="text-sm text-orange-600 dark:text-orange-400 mb-3">
                                                Only {{ $product->quantity }} left in stock
                                            </p>
                                        @endif

                                        <!-- Add to Cart Button -->
                                        @if($product->quantity > 0)
                                            <button onclick="addToCartFromListing({{ $product->id }}, '{{ $product->name }}')" 
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                Add to Cart
                                            </button>
                                        @else
                                            <button disabled 
                                                class="w-full bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg cursor-not-allowed">
                                                Out of Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">No products found</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Try adjusting your search or filters</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2 w-96 max-w-full"></div>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `transform transition-all duration-300 ease-in-out translate-x-full opacity-0 bg-white dark:bg-gray-800 shadow-xl rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden`;
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' 
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>';
            
            toast.innerHTML = `
                <div class="flex items-center p-4">
                    <div class="flex-shrink-0">
                        <div class="${bgColor} rounded-full p-2">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                ${icon}
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${message}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="inline-flex text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
            
            const container = document.getElementById('toast-container');
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function addToCartFromListing(productId, productName) {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id: productId, quantity: 1 });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartBadge();
            showToast(`${productName} added to cart!`, 'success');
        }

        function updateCartBadge() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const badge = document.getElementById('cart-badge');
            
            if (badge) {
                if (totalItems > 0) {
                    badge.textContent = totalItems;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }
    </script>

    @include('partials.footer')
@endsection
