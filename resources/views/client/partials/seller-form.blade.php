<div class="w-full bg-white p-6 rounded-lg shadow-md space-y-4">
    <h3 class="text-lg font-bold mb-4">Maklumat Penjual</h3>
    <p class="text-sm text-gray-600 mb-4"><span class="text-red-500">*</span> Menandakan medan yang wajib diisi</p>
    
    @if(auth()->user()->is_seller)
        @if(auth()->user()->seller_status === 'pending')
            <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium text-yellow-800">Permohonan Sedang Menunggu</span>
                </div>
                <p class="text-yellow-700 text-sm">Permohonan anda sedang dalam proses semakan oleh admin. Anda akan diberitahu sebaik sahaja keputusan dikeluarkan.</p>
            </div>
        @elseif(auth()->user()->seller_status === 'rejected')
            <div class="bg-red-100 border border-red-300 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="font-medium text-red-800">Permohonan Ditolak</span>
                </div>
                @if(auth()->user()->seller_rejection_reason)
                    <p class="text-red-700 text-sm mb-2"><strong>Sebab Penolakan:</strong></p>
                    <p class="text-red-700 text-sm">{{ auth()->user()->seller_rejection_reason }}</p>
                @endif
                <p class="text-red-700 text-sm mt-2">Anda boleh menghantar semula permohonan dengan maklumat yang dikemaskini.</p>
            </div>
        @elseif(auth()->user()->seller_status === 'approved')
            <div class="bg-green-100 border border-green-300 rounded-lg p-4 mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-medium text-green-800">Permohonan Diluluskan</span>
                </div>
                <p class="text-green-700 text-sm">Permohonan anda telah diluluskan! Anda kini boleh menambah perkhidmatan dan produk.</p>
            </div>
        @endif
    @endif
    
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(!auth()->user()->is_seller || auth()->user()->seller_status === 'rejected')
        <form method="POST" action="{{ route('dashboard.become_seller') }}" enctype="multipart/form-data" id="sellerForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Bio <span class="text-red-500">*</span></label>
                    <textarea name="bio" class="w-full border rounded p-2" required>{{ old('bio', auth()->user()->bio) }}</textarea>
                </div>
                <div>
                    <label class="block font-medium">Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="location" class="w-full border rounded p-2" value="{{ old('location', auth()->user()->location) }}" required>
                </div>
                <div>
                    <label class="block font-medium">No. Telefon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" class="w-full border rounded p-2" value="{{ old('phone', auth()->user()->phone) }}" required>
                </div>
                <div>
                    <label class="block font-medium">Nama Perniagaan <span class="text-red-500">*</span></label>
                    <input type="text" name="business_name" class="w-full border rounded p-2" value="{{ old('business_name', auth()->user()->business_name) }}" required>
                </div>
                <div>
                    <label class="block font-medium">Jenis Perniagaan <span class="text-red-500">*</span></label>
                    <select name="business_type" class="w-full border rounded p-2" required onchange="toggleSelfieField()">
                        <option value="">Pilih</option>
                        <option value="individual" {{ old('business_type', auth()->user()->business_type)=='individual' ? 'selected' : '' }}>Individu</option>
                        <option value="company" {{ old('business_type', auth()->user()->business_type)=='company' ? 'selected' : '' }}>Syarikat</option>
                        <option value="freelance" {{ old('business_type', auth()->user()->business_type)=='freelance' ? 'selected' : '' }}>Freelance</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">No. Pendaftaran Perniagaan <span class="text-red-500" id="businessRegAsterisk" style="display: none;">*</span></label>
                    <input type="text" name="business_registration" class="w-full border rounded p-2" value="{{ old('business_registration', auth()->user()->business_registration) }}" id="businessRegInput">
                </div>
                <div>
                    <label class="block font-medium">Alamat Perniagaan <span class="text-red-500">*</span></label>
                    <input type="text" name="business_address" class="w-full border rounded p-2" value="{{ old('business_address', auth()->user()->business_address) }}" required>
                </div>
                <div>
                    <label class="block font-medium">Kawasan Operasi / Wilayah <span class="text-red-500">*</span></label>
                    <input type="text" name="operating_area" class="w-full border rounded p-2" value="{{ old('operating_area', auth()->user()->operating_area) }}" required>
                </div>
                <div>
                    <label class="block font-medium">Laman Web / Media Sosial</label>
                    <input type="text" name="website" class="w-full border rounded p-2" value="{{ old('website', auth()->user()->website) }}">
                </div>
                <div>
                    <label class="block font-medium">Tahun Pengalaman <span class="text-red-500">*</span></label>
                    <input type="number" name="years_experience" class="w-full border rounded p-2" value="{{ old('years_experience', auth()->user()->years_experience) }}" min="0" required>
                </div>
                <div>
                    <label class="block font-medium">Kemahiran / Tag / Kepakaran <span class="text-red-500">*</span></label>
                    <input type="text" name="skills" class="w-full border rounded p-2" value="{{ old('skills', auth()->user()->skills) }}" placeholder="Contoh: Grafik, Fotografi, Jualan" required>
                </div>
                <div>
                    <label class="block font-medium">Kawasan Perkhidmatan / Liputan <span class="text-red-500">*</span></label>
                    <input type="text" name="service_areas" class="w-full border rounded p-2" value="{{ old('service_areas', auth()->user()->service_areas) }}" required>
                </div>
                <div>
                    <label class="block font-medium">Muat Naik Kad Pengenalan / Sijil / Lesen Perniagaan <span class="text-red-500">*</span></label>
                    <input type="file" name="id_document" class="w-full border rounded p-2" accept="image/*,application/pdf" required>
                </div>
                <div id="selfieField">
                    <label class="block font-medium">Selfie Bersama Kad Pengenalan <span class="text-red-500">*</span></label>
                    <input type="file" name="selfie_with_id" class="w-full border rounded p-2" accept="image/*" required>
                </div>
            </div>
        </form>
        
        <script>
            function toggleSelfieField() {
                const businessType = document.querySelector('select[name="business_type"]').value;
                const selfieField = document.getElementById('selfieField');
                const businessRegInput = document.getElementById('businessRegInput');
                const businessRegAsterisk = document.getElementById('businessRegAsterisk');
                
                if (businessType === 'company') {
                    selfieField.style.display = 'none';
                    selfieField.querySelector('input').removeAttribute('required');
                    
                    // Make business registration required for company
                    businessRegInput.setAttribute('required', 'required');
                    businessRegAsterisk.style.display = 'inline';
                } else {
                    selfieField.style.display = 'block';
                    selfieField.querySelector('input').setAttribute('required', 'required');
                    
                    // Make business registration optional for other types
                    businessRegInput.removeAttribute('required');
                    businessRegAsterisk.style.display = 'none';
                }
            }
            
            // Run on page load to set initial state
            document.addEventListener('DOMContentLoaded', function() {
                toggleSelfieField();
            });
        </script>
        
        <div class="flex gap-3">
            <button type="submit" form="sellerForm" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ auth()->user()->seller_status === 'rejected' ? 'Hantar Semula Permohonan' : 'Hantar Permohonan' }}
            </button>
            <form method="POST" action="{{ route('dashboard.hide_seller_form') }}">
                @csrf
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </button>
            </form>
        </div>
    @else
        <div class="flex gap-3">
            <form method="POST" action="{{ route('dashboard.hide_seller_form') }}">
                @csrf
                <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tutup
                </button>
            </form>
        </div>
    @endif
</div> 