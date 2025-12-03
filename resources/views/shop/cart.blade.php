@extends('layouts.main')

@section('title', 'Shopping Cart | My Portfolio')
@section('description', 'Review your cart')

@section('content')
    @include('partials.navbar')
    
    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="pt-16">
            <!-- Hero Section -->
            <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-12">
                <div class="max-w-7xl mx-auto px-4">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">Shopping Cart</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Review and manage your items</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <div id="cart-container">
                    <!-- Loading State -->
                    <div id="loading-state" class="text-center py-16">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">Loading cart...</p>
                    </div>

                    <!-- Empty Cart State -->
                    <div id="empty-cart" class="hidden text-center py-16">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Your cart is empty</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Add some products to get started</p>
                        <a href="{{ route('shop.index') }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-all duration-300">
                            Continue Shopping
                        </a>
                    </div>

                    <!-- Cart Items -->
                    <div id="cart-items" class="hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Items List -->
                            <div class="lg:col-span-2 space-y-4" id="items-list"></div>

                            <!-- Cart Summary -->
                            <div class="lg:col-span-1">
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 sticky top-24">
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>
                                    
                                    <div class="space-y-4 mb-6">
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Subtotal</span>
                                            <span id="subtotal">0₫</span>
                                        </div>
                                        <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                            <span>Shipping</span>
                                            <span class="text-green-600 dark:text-green-400">Free</span>
                                        </div>
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                            <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                                                <span>Total</span>
                                                <span id="total">0₫</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Coupon Code -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Coupon Code</label>
                                        <div class="flex gap-2">
                                            <input type="text" 
                                                id="coupon-code" 
                                                placeholder="Enter code"
                                                class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <button onclick="applyCoupon()" 
                                                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-all">
                                                Apply
                                            </button>
                                        </div>
                                        <div id="coupon-message" class="mt-2 text-sm"></div>
                                    </div>

                                    <button onclick="window.location.href='{{ route('checkout') }}'" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        Proceed to Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cartData = [];
        let couponDiscount = 0;

        async function loadCart() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            
            if (cart.length === 0) {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('empty-cart').classList.remove('hidden');
                return;
            }

            const productIds = cart.map(item => item.id);
            
            try {
                const response = await fetch('{{ route('cart.items') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: productIds })
                });

                const products = await response.json();
                cartData = cart.map(cartItem => {
                    const product = products.find(p => p.id === cartItem.id);
                    return product ? { ...product, cartQuantity: cartItem.quantity } : null;
                }).filter(item => item !== null);

                renderCart();
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        function renderCart() {
            if (cartData.length === 0) {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('empty-cart').classList.remove('hidden');
                return;
            }

            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('cart-items').classList.remove('hidden');

            const itemsList = document.getElementById('items-list');
            itemsList.innerHTML = cartData.map(item => `
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex gap-4">
                        <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                            ${item.image ? 
                                `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">` :
                                `<div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>`
                            }
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">${item.name}</h3>
                            <p class="text-xl font-bold text-gray-900 dark:text-white mb-3">${formatPrice(item.price)}₫</p>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <button onclick="updateQuantity(${item.id}, -1)" 
                                        class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="w-12 text-center font-semibold text-gray-900 dark:text-white">${item.cartQuantity}</span>
                                    <button onclick="updateQuantity(${item.id}, 1)" 
                                        class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center"
                                        ${item.cartQuantity >= item.quantity ? 'disabled' : ''}>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>
                                </div>
                                <button onclick="removeItem(${item.id})" 
                                    class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            updateSummary();
        }

        function updateQuantity(productId, change) {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            const item = cart.find(i => i.id === productId);
            const product = cartData.find(p => p.id === productId);
            
            if (item && product) {
                item.quantity += change;
                
                if (item.quantity <= 0) {
                    removeItem(productId);
                    return;
                }
                
                if (item.quantity > product.quantity) {
                    alert('Not enough stock available');
                    return;
                }
                
                localStorage.setItem('cart', JSON.stringify(cart));
                loadCart();
            }
        }

        function removeItem(productId) {
            let cart = JSON.parse(localStorage.getItem('cart') || '[]');
            cart = cart.filter(item => item.id !== productId);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }

        function updateSummary() {
            const subtotal = cartData.reduce((sum, item) => sum + (item.price * item.cartQuantity), 0);
            const discount = subtotal * (couponDiscount / 100);
            const total = subtotal - discount;
            
            document.getElementById('subtotal').textContent = formatPrice(subtotal) + '₫';
            document.getElementById('total').textContent = formatPrice(total) + '₫';
        }

        function formatPrice(price) {
            return Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function applyCoupon() {
            const code = document.getElementById('coupon-code').value.trim();
            const messageEl = document.getElementById('coupon-message');
            
            if (!code) {
                messageEl.textContent = 'Please enter a coupon code';
                messageEl.className = 'mt-2 text-sm text-red-600 dark:text-red-400';
                return;
            }

            if (code.toUpperCase() === 'SAVE10') {
                couponDiscount = 10;
                messageEl.textContent = '10% discount applied!';
                messageEl.className = 'mt-2 text-sm text-green-600 dark:text-green-400';
                updateSummary();
            } else if (code.toUpperCase() === 'SAVE20') {
                couponDiscount = 20;
                messageEl.textContent = '20% discount applied!';
                messageEl.className = 'mt-2 text-sm text-green-600 dark:text-green-400';
                updateSummary();
            } else {
                messageEl.textContent = 'Invalid coupon code';
                messageEl.className = 'mt-2 text-sm text-red-600 dark:text-red-400';
            }
        }

        document.addEventListener('DOMContentLoaded', loadCart);
    </script>

    @include('partials.footer')
@endsection
