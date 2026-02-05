<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Apotek App') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900" x-data="medicineApp()">

    <!-- Header -->
    <header class="bg-white shadow sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                <h1 class="text-xl font-bold text-gray-800">Apotek Public</h1>
            </div>
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    <div class="hidden md:flex gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-blue-600 font-medium">Log in</a>
                        @endauth
                    </div>
                @endif
                
                <button @click="isCartOpen = true" class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-show="cart.length > 0" x-text="cartItemCount" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Available Medicines</h2>
            <p class="text-gray-600 mt-1">Order high-quality medicines directly.</p>
        </div>

        <!-- Loading State -->
        <div x-show="isLoading && medicines.length === 0" class="flex justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && medicines.length === 0" class="text-center py-12 hidden" :class="{'hidden': isLoading || medicines.length > 0}">
            <p class="text-gray-500 text-lg">No medicines available at the moment.</p>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <template x-for="medicine in medicines" :key="medicine.id">
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div class="p-4 flex-grow">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-semibold text-gray-800 line-clamp-2" x-text="medicine.nama_obat"></h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-show="medicine.stok_obat > 10">
                                In Stock
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800" x-show="medicine.stok_obat <= 10 && medicine.stok_obat > 0">
                                Low Stock
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800" x-show="medicine.stok_obat === 0">
                                Out of Stock
                            </span>
                        </div>
                        <p class="text-gray-500 text-sm mt-1">Stock: <span x-text="medicine.stok_obat"></span></p>
                        <div class="mt-4">
                            <span class="text-xl font-bold text-blue-600" x-text="formatCurrency(medicine.harga_obat)"></span>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 border-t border-gray-100">
                        <button 
                            @click="addToCart(medicine)"
                            :disabled="medicine.stok_obat === 0"
                            class="w-full flex justify-center items-center gap-2 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Cart Sidebar Overlay -->
    <div 
        x-show="isCartOpen" 
        class="fixed inset-0 overflow-hidden z-20" 
        style="display: none;"
    >
        <div class="absolute inset-0 overflow-hidden">
            <!-- Background overlay -->
            <div 
                x-show="isCartOpen"
                x-transition:enter="ease-in-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in-out duration-500"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                @click="isCartOpen = false"
            ></div>

            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div 
                    x-show="isCartOpen"
                    x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md"
                >
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-lg font-medium text-gray-900">Shopping Cart</h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="isCartOpen = false" type="button" class="-m-2 p-2 text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Close panel</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div class="flow-root">
                                    <ul role="list" class="-my-6 divide-y divide-gray-200">
                                        <template x-for="(item, index) in cart" :key="item.medicine_id">
                                            <li class="py-6 flex">
                                                <div class="flex-1 flex flex-col">
                                                    <div>
                                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                                            <h3 x-text="item.nama_obat"></h3>
                                                            <p class="ml-4" x-text="formatCurrency(item.price * item.quantity)"></p>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500" x-text="formatCurrency(item.price) + ' / unit'"></p>
                                                    </div>
                                                    <div class="flex-1 flex items-end justify-between text-sm">
                                                        <div class="flex items-center border border-gray-300 rounded">
                                                            <button @click="updateQuantity(index, -1)" class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50" :disabled="item.quantity <= 1">-</button>
                                                            <span class="px-2 py-1 text-gray-900 font-medium" x-text="item.quantity"></span>
                                                            <button @click="updateQuantity(index, 1)" class="px-2 py-1 text-gray-600 hover:bg-gray-100 disabled:opacity-50" :disabled="item.quantity >= item.max_stock">+</button>
                                                        </div>

                                                        <div class="flex">
                                                            <button @click="removeFromCart(index)" type="button" class="font-medium text-red-600 hover:text-red-500">Remove</button>
                                                        </div>
                                                    </div>
                                                    <p x-show="item.quantity >= item.max_stock" class="text-xs text-red-500 mt-1">Max stock reached</p>
                                                </div>
                                            </li>
                                        </template>
                                        <li x-show="cart.length === 0" class="py-6 text-center text-gray-500">
                                            Your cart is empty.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 py-6 px-4 sm:px-6" x-show="cart.length > 0">
                            <div class="flex justify-between text-base font-medium text-gray-900">
                                <p>Subtotal</p>
                                <p x-text="formatCurrency(cartTotal)"></p>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500">Shipping and taxes calculated at checkout.</p>
                            <div class="mt-6">
                                <button 
                                    @click="checkout" 
                                    :disabled="isLoading"
                                    class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed"
                                >
                                    <span x-show="!isLoading">Checkout</span>
                                    <span x-show="isLoading" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                            <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
                                <p>
                                    or <button @click="isCartOpen = false" type="button" class="text-blue-600 font-medium hover:text-blue-500">Continue Shopping<span aria-hidden="true"> &rarr;</span></button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div 
        x-show="orderSuccess" 
        class="fixed z-30 inset-0 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="orderSuccess" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="orderSuccess"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full sm:p-6"
            >
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Order Successful!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Your order has been placed successfully.
                                <br>
                                Transaction ID: <span class="font-mono font-bold" x-text="lastOrder?.id"></span>
                            </p>
                            <p class="text-sm text-gray-500 mt-2">
                                Total: <span class="font-bold text-gray-900" x-text="formatCurrency(lastOrder?.total_amount)"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button 
                        @click="orderSuccess = false" 
                        type="button" 
                        class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm"
                    >
                        Close & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('medicineApp', () => ({
                medicines: [],
                cart: [],
                isCartOpen: false,
                orderSuccess: false,
                lastOrder: null,
                isLoading: false,

                async init() {
                    this.fetchMedicines();
                },

                async fetchMedicines() {
                    this.isLoading = true;
                    try {
                        // Use axios directly as it's provided by Laravel's bootstrap.js
                        const response = await axios.get('/api/medicines');
                        this.medicines = response.data.data;
                    } catch (error) {
                        console.error('Error fetching medicines:', error);
                        // Fallback if axios isn't ready for some reason (rare in standard setup)
                    } finally {
                        this.isLoading = false;
                    }
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value || 0);
                },

                addToCart(medicine) {
                    const existingItem = this.cart.find(item => item.medicine_id === medicine.id);
                    if (existingItem) {
                        if (existingItem.quantity < medicine.stok_obat) {
                            existingItem.quantity++;
                        } else {
                            // Shake or alert
                        }
                    } else {
                        this.cart.push({
                            medicine_id: medicine.id,
                            nama_obat: medicine.nama_obat,
                            price: medicine.harga_obat,
                            quantity: 1,
                            max_stock: medicine.stok_obat
                        });
                    }
                    this.isCartOpen = true;
                },

                updateQuantity(index, change) {
                    const item = this.cart[index];
                    const newQty = item.quantity + change;
                    if (newQty >= 1 && newQty <= item.max_stock) {
                        item.quantity = newQty;
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                get cartItemCount() {
                    return this.cart.reduce((total, item) => total + item.quantity, 0);
                },

                get cartTotal() {
                    return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                },

                async checkout() {
                    if (this.cart.length === 0) return;
                    this.isLoading = true;
                    
                    try {
                        const payload = {
                            transaction_date: new Date().toISOString().split('T')[0],
                            items: this.cart.map(item => ({
                                medicine_id: item.medicine_id,
                                quantity: item.quantity
                            }))
                        };

                        const response = await axios.post('/api/orders', payload);
                        
                        if (response.data.status === 'success') {
                            this.lastOrder = response.data.data;
                            this.orderSuccess = true;
                            this.cart = [];
                            this.isCartOpen = false;
                            this.fetchMedicines(); // Refresh stock
                        }
                    } catch (error) {
                        const msg = error.response?.data?.message || 'Checkout failed';
                        alert(msg);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>