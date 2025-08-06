@extends('layouts.app')

@section('title', 'Pembayaran Stripe')
@section('meta_description', 'Selesaikan pembayaran anda dengan selamat melalui Stripe')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Pembayaran Stripe</h1>
            <p class="text-gray-600 mt-2">Selesaikan pembayaran anda dengan selamat</p>
        </div>

        <!-- Payment Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Maklumat Kad</h2>
            </div>
            
            <div class="p-6">
                <form id="payment-form">
                    <div class="space-y-6">
                        <!-- Card Element -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Maklumat Kad Kredit/Debit
                            </label>
                            <div id="card-element" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <div id="card-errors" class="text-red-600 text-sm mt-2" role="alert"></div>
                        </div>

                        <!-- Payment Button -->
                        <button type="submit" id="submit-button" 
                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="button-text">Bayar Sekarang</span>
                            <div id="spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </button>

                        <!-- Security Notice -->
                        <div class="text-center">
                            <p class="text-sm text-gray-600">
                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Pembayaran anda dilindungi dengan SSL encryption
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cancel Payment -->
        <div class="text-center mt-6">
            <button onclick="showCancelModal()" class="text-red-600 hover:text-red-700 font-medium cursor-pointer">
                ✕ Batal Pembayaran
            </button>
        </div>
    </div>
</div>

<!-- Cancel Payment Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Batal Pembayaran</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Pesanan anda telah dicipta dan boleh dibayar semula di halaman "Pesanan Saya".
                </p>
                <p class="text-xs text-gray-400 mb-4">
                    Anda akan dialihkan ke halaman pesanan untuk melihat butiran pesanan anda.
                </p>
            </div>
            <div class="flex items-center justify-center space-x-3">
                <button onclick="hideCancelModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Teruskan Pembayaran
                </button>
                <button onclick="goToOrders()" 
                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                    Lihat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<!-- Stripe Scripts -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();

    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });

    // Mount card element
    cardElement.mount('#card-element');

    // Handle form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        // Disable button and show spinner
        submitButton.disabled = true;
        buttonText.style.display = 'none';
        spinner.classList.remove('hidden');

        // Clear any previous errors
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = '';

        try {
            // Confirm payment
            const { error, paymentIntent } = await stripe.confirmCardPayment('{{ $clientSecret }}', {
                payment_method: {
                    card: cardElement,
                }
            });

            if (error) {
                // Show error message
                errorElement.textContent = error.message;
                
                // Re-enable button
                submitButton.disabled = false;
                buttonText.style.display = 'inline';
                spinner.classList.add('hidden');
            } else {
                // Payment successful, show success message before redirect
                errorElement.textContent = 'Pembayaran berjaya! Mengalihkan...';
                errorElement.className = 'text-green-600 text-sm mt-2';
                
                // Redirect to return URL after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route("checkout.stripe.return") }}?payment_intent=' + paymentIntent.id + '&payment_intent_client_secret=' + paymentIntent.client_secret;
                }, 1000);
            }
        } catch (err) {
            // Handle unexpected errors
            errorElement.textContent = 'Ralat tidak dijangka berlaku. Sila cuba lagi.';
            
            // Re-enable button
            submitButton.disabled = false;
            buttonText.style.display = 'inline';
            spinner.classList.add('hidden');
            
            console.error('Payment error:', err);
        }
    });

    // Handle card element errors
    cardElement.addEventListener('change', ({error}) => {
        const displayError = document.getElementById('card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Cancel modal functions
    function showCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function goToOrders() {
        window.location.href = '{{ route("checkout.orders") }}';
    }

    // Close modal when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideCancelModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideCancelModal();
        }
    });
</script>
@endsection 