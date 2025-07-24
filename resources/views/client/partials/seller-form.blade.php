<div class="w-full bg-white p-6 rounded-lg shadow-md space-y-4">
    <h3 class="text-lg font-bold mb-4">Maklumat Penjual</h3>
    @if($errors->any())
        <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('dashboard.become_seller') }}" enctype="multipart/form-data" id="sellerForm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Bio</label>
                <textarea name="bio" class="w-full border rounded p-2" required>{{ old('bio', auth()->user()->bio) }}</textarea>
            </div>
            <div>
                <label class="block font-medium">Lokasi</label>
                <input type="text" name="location" class="w-full border rounded p-2" value="{{ old('location', auth()->user()->location) }}" required>
            </div>
            <div>
                <label class="block font-medium">No. Telefon</label>
                <input type="text" name="phone" class="w-full border rounded p-2" value="{{ old('phone', auth()->user()->phone) }}" required>
            </div>
            <div>
                <label class="block font-medium">Nama Perniagaan</label>
                <input type="text" name="business_name" class="w-full border rounded p-2" value="{{ old('business_name') }}" required>
            </div>
            <div>
                <label class="block font-medium">Jenis Perniagaan</label>
                <select name="business_type" class="w-full border rounded p-2" required>
                    <option value="">Pilih</option>
                    <option value="individual" {{ old('business_type')=='individual' ? 'selected' : '' }}>Individu</option>
                    <option value="company" {{ old('business_type')=='company' ? 'selected' : '' }}>Syarikat</option>
                    <option value="freelance" {{ old('business_type')=='freelance' ? 'selected' : '' }}>Freelance</option>
                    <option value="other" {{ old('business_type')=='other' ? 'selected' : '' }}>Lain-lain</option>
                </select>
            </div>
            <div>
                <label class="block font-medium">No. Pendaftaran Perniagaan (Opsyenal)</label>
                <input type="text" name="business_registration" class="w-full border rounded p-2" value="{{ old('business_registration') }}">
            </div>
            <div>
                <label class="block font-medium">Alamat Perniagaan</label>
                <input type="text" name="business_address" class="w-full border rounded p-2" value="{{ old('business_address') }}" required>
            </div>
            <div>
                <label class="block font-medium">Kawasan Operasi / Wilayah</label>
                <input type="text" name="operating_area" class="w-full border rounded p-2" value="{{ old('operating_area') }}" required>
            </div>
            <div>
                <label class="block font-medium">Laman Web / Media Sosial</label>
                <input type="text" name="website" class="w-full border rounded p-2" value="{{ old('website') }}">
            </div>
            <div>
                <label class="block font-medium">Tahun Pengalaman</label>
                <input type="number" name="years_experience" class="w-full border rounded p-2" value="{{ old('years_experience') }}" min="0" required>
            </div>
            <div>
                <label class="block font-medium">Kemahiran / Tag / Kepakaran</label>
                <input type="text" name="skills" class="w-full border rounded p-2" value="{{ old('skills') }}" placeholder="Contoh: Grafik, Fotografi, Jualan" required>
            </div>
            <div>
                <label class="block font-medium">Kawasan Perkhidmatan / Liputan</label>
                <input type="text" name="service_areas" class="w-full border rounded p-2" value="{{ old('service_areas') }}" required>
            </div>
            <div>
                <label class="block font-medium">Muat Naik Kad Pengenalan / Sijil / Lesen Perniagaan</label>
                <input type="file" name="id_document" class="w-full border rounded p-2" accept="image/*,application/pdf" required>
            </div>
            <div>
                <label class="block font-medium">Selfie Bersama Kad Pengenalan</label>
                <input type="file" name="selfie_with_id" class="w-full border rounded p-2" accept="image/*" required>
            </div>
        </div>
    </form>
    
    <div class="flex gap-3">
        <button type="submit" form="sellerForm" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Hantar Permohonan
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
</div> 