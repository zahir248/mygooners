<div x-data="{ open: false }" class="inline-flex items-center">
    <!-- Trigger Button -->
    <button type="button" @click="open = true" class="{{ $buttonClass ?? 'text-red-600 hover:text-red-800 text-sm font-medium' }}">
        {{ $buttonText ?? 'Batal' }}
    </button>

    <!-- Modal -->
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div @click.away="open = false" class="bg-white rounded-xl shadow-lg max-w-sm w-full p-6 relative">
            <!-- Close Button -->
            <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Icon -->
            <div class="flex items-center justify-center mb-4">
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
            <!-- Title -->
            <h3 class="text-lg font-bold text-gray-900 mb-2 text-center">{{ $buttonText ?? 'Batal' }} Permohonan?</h3>
            <!-- Message -->
            <div class="text-gray-700 text-sm mb-6 text-center">
                {{ $message ?? 'Adakah anda pasti mahu membatalkan permohonan ini?' }}
            </div>
            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button @click="open = false" type="button" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 rounded-lg transition-colors">Tidak</button>
                <form x-ref="form" method="POST" action="{{ $action }}" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition-colors">
                        Ya, {{ $buttonText ?? 'Batal' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div> 