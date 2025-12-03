@extends('layouts.main')

@section('title', 'Checkout | My Portfolio')
@section('description', 'Complete your order')

@section('content')
    @include('partials.navbar')
    
    <div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-500">
        <div class="pt-16">
            <!-- Hero Section -->
            <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-12">
                <div class="max-w-7xl mx-auto px-4">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">Checkout</h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">Complete your order</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Checkout Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Customer Information</h2>
                            
                            <form id="checkout-form" class="space-y-6">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                        id="customer_name" 
                                        name="customer_name" 
                                        required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                        id="customer_email" 
                                        name="customer_email" 
                                        required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                        id="customer_phone" 
                                        name="customer_phone" 
                                        required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                </div>

                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Shipping Address <span class="text-red-500">*</span>
                                    </label>
                                    <textarea 
                                        id="shipping_address" 
                                        name="shipping_address" 
                                        rows="4" 
                                        required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                                </div>

                                <button type="submit" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    Place Order
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 sticky top-24">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Order Summary</h2>
                            
                            <div id="order-items" class="space-y-4 mb-6"></div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>Subtotal</span>
                                    <span id="checkout-subtotal">0₫</span>
                                </div>
                                <div class="flex justify-between text-gray-700 dark:text-gray-300">
                                    <span>Shipping</span>
                                    <span class="text-green-600 dark:text-green-400">Free</span>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white">
                                        <span>Total</span>
                                        <span id="checkout-total">0₫</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2 w-96 max-w-full"></div>

    <script>
        let cartData = [];

        async function loadCheckoutCart() {
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            
            if (cart.length === 0) {
                window.location.href = '{{ route('cart') }}';
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

                renderCheckoutSummary();
            } catch (error) {
                console.error('Error loading cart:', error);
            }
        }

        function renderCheckoutSummary() {
            const itemsList = document.getElementById('order-items');
            itemsList.innerHTML = cartData.map(item => `
                <div class="flex gap-3">
                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                        ${item.image ? 
                            `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">` :
                            `<div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                </svg>
                            </div>`
                        }
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">${item.name}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Qty: ${item.cartQuantity}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white">${formatPrice(item.price * item.cartQuantity)}₫</p>
                    </div>
                </div>
            `).join('');

            updateCheckoutSummary();
        }

        function updateCheckoutSummary() {
            const subtotal = cartData.reduce((sum, item) => sum + (item.price * item.cartQuantity), 0);
            document.getElementById('checkout-subtotal').textContent = formatPrice(subtotal) + '₫';
            document.getElementById('checkout-total').textContent = formatPrice(subtotal) + '₫';
        }

        function formatPrice(price) {
            return Math.round(price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

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

        document.getElementById('checkout-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            
            const data = {
                customer_name: formData.get('customer_name'),
                customer_email: formData.get('customer_email'),
                customer_phone: formData.get('customer_phone'),
                shipping_address: formData.get('shipping_address'),
                cart: cart
            };

            try {
                const response = await fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success) {
                    localStorage.removeItem('cart');
                    showToast('Order placed successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route('shop.index') }}';
                    }, 2000);
                } else {
                    showToast('Failed to place order. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('An error occurred. Please try again.', 'error');
            }
        });

        document.addEventListener('DOMContentLoaded', loadCheckoutCart);
    </script>

    @include('partials.footer')
@endsection
