@extends('layouts.main')

@section('title', $product->name . ' | My Portfolio')
@section('description', $product->description ?? 'Product details')

@section('content')
    @include('partials.navbar')
    
    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="pt-16">
            <!-- Breadcrumb -->
            <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-6">
                <div class="max-w-7xl mx-auto px-4">
                    <nav class="flex items-center space-x-2 text-sm">
                        <a href="{{ route('shop.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Shop</a>
                        <span class="text-gray-400">/</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $product->name }}</span>
                    </nav>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Product Detail -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                    <!-- Product Images -->
                    <div class="space-y-4">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 shadow-xl">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                    alt="{{ $product->name }}" 
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-32 h-32 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="flex flex-col">
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $product->name }}</h1>
                        
                        <!-- Price -->
                        <div class="mb-6">
                            @if($product->sale_price && $product->sale_price < $product->price)
                                <div class="flex items-center gap-3">
                                    <span class="text-4xl font-bold text-red-600 dark:text-red-400">
                                        {{ number_format($product->sale_price, 0, ',', '.') }}₫
                                    </span>
                                    <span class="text-2xl text-gray-500 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}₫
                                    </span>
                                    <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                        -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                    </span>
                                </div>
                            @else
                                <span class="text-4xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </span>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-6">
                            @if($product->quantity > 0)
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-green-600 dark:text-green-400 font-medium">In Stock</span>
                                    @if($product->quantity <= 10)
                                        <span class="text-orange-600 dark:text-orange-400 ml-2">
                                            (Only {{ $product->quantity }} left)
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <span class="text-red-600 dark:text-red-400 font-medium">Out of Stock</span>
                                </div>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="mb-8 prose dark:prose-invert max-w-none">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Description</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $product->description }}</p>
                        </div>

                        <!-- Action Buttons -->
                        @if($product->quantity > 0)
                            <div class="flex gap-3">
                                <button onclick="addToCart({{ $product->id }})" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Thêm vào giỏ
                                </button>
                                <a href="{{ route('shop.index') }}" 
                                    class="flex-1 bg-white dark:bg-gray-800 border-2 border-blue-600 dark:border-blue-400 text-blue-600 dark:text-blue-400 font-bold py-4 px-8 rounded-lg transition-all duration-300 hover:bg-blue-50 dark:hover:bg-gray-700 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Tiếp tục mua sắm
                                </a>
                            </div>
                        @else
                            <button disabled 
                                class="w-full bg-gray-400 text-white font-bold py-4 px-8 rounded-lg cursor-not-allowed">
                                Out of Stock
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Related Products -->
                @if($relatedProducts->count() > 0)
                    <div class="mt-16">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">You May Also Like</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($relatedProducts as $related)
                                <a href="{{ route('shop.show', $related->slug) }}" class="group">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-200 dark:border-gray-700">
                                        <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                                            @if($related->image)
                                                <img src="{{ asset('storage/' . $related->image) }}" 
                                                    alt="{{ $related->name }}" 
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                    loading="lazy">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-20 h-20 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ $related->name }}
                                            </h3>
                                            <div class="flex items-center gap-2">
                                                @if($related->sale_price && $related->sale_price < $related->price)
                                                    <span class="text-xl font-bold text-red-600 dark:text-red-400">
                                                        {{ number_format($related->sale_price, 0, ',', '.') }}₫
                                                    </span>
                                                    <span class="text-sm text-gray-500 line-through">
                                                        {{ number_format($related->price, 0, ',', '.') }}₫
                                                    </span>
                                                @else
                                                    <span class="text-xl font-bold text-gray-900 dark:text-white">
                                                        {{ number_format($related->price, 0, ',', '.') }}₫
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
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

        function addToCart(productId) {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id: productId, quantity: 1 });
            }
            
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartBadge();
            showToast('Product added to cart!', 'success');
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
