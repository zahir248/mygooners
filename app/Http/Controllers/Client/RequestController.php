<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    public function showServiceRequestForm()
    {
        return view('client.requests.service-request');
    }

    public function storeServiceRequest(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Generate slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;

        // Ensure unique slug
        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle tags
        $tags = null;
        if (!empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        // Create service with pending status
        $service = Service::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'location' => $validated['location'],
            'pricing' => $validated['pricing'],
            'contact_info' => $validated['contact_info'],
            'category' => $validated['category'],
            'tags' => $tags,
            'images' => $images,
            'status' => 'pending'
        ]);

        return redirect()->route('dashboard')->with('success', 'Permohonan perkhidmatan anda telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function previewPendingService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        return view('client.requests.pending-service-preview', compact('service'));
    }

    public function previewRejectedService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.rejected-service-preview', compact('service'));
    }

    public function previewOwnService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        return view('client.requests.service-preview', compact('service'));
    }

    public function editRejectedService($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        return view('client.requests.edit-rejected-service', compact('service'));
    }

    public function updateRejectedService(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($service->title !== $request->title) {
            $hasChanges = true;
            $changes[] = 'Tajuk perkhidmatan';
        }
        if ($service->description !== $request->description) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($service->location !== $request->location) {
            $hasChanges = true;
            $changes[] = 'Lokasi';
        }
        if ($service->pricing !== $request->pricing) {
            $hasChanges = true;
            $changes[] = 'Harga';
        }
        if ($service->contact_info !== $request->contact_info) {
            $hasChanges = true;
            $changes[] = 'Maklumat hubungan';
        }
        if ($service->category !== $request->category) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }

        // Check tags
        $newTags = $request->tags ? explode(',', $request->tags) : [];
        $currentTags = is_array($service->tags) ? $service->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($service->images) ? $service->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // If no changes, show reminder
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat berdasarkan sebab penolakan sebelum menghantar semula.')
                ->withInput();
        }

        // Update basic fields
        $service->title = $request->title;
        $service->slug = Str::slug($request->title);
        $service->description = $request->description;
        $service->location = $request->location;
        $service->pricing = $request->pricing;
        $service->contact_info = $request->contact_info;
        $service->category = $request->category;
        $service->tags = $newTags;
        $service->status = 'pending';
        $service->rejection_reason = null; // Clear rejection reason

        // Handle images
        $newImages = [];
        
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $filename = 'services/' . time() . '_' . Str::slug($request->title) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename),
                    $image,
                    basename($filename)
                );
                if ($stored) {
                    $newImages[] = $filename;
                }
            }
        }
        
        $service->images = array_merge($currentImages, $newImages);
        $service->save();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan berjaya dihantar semula!');
    }

    public function updateServiceStatus(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $service->status = $request->status;
        $service->save();

        $statusText = $request->status === 'active' ? 'aktif' : 'tidak aktif';
        return redirect()->route('dashboard')
            ->with('success', "Status perkhidmatan berjaya dikemaskini kepada {$statusText}!");
    }

    public function showServiceEditRequestForm($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        return view('client.requests.service-edit-request', compact('service'));
    }

    public function storeServiceEditRequest(Request $request, $id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'inactive'])
            ->firstOrFail();

        // Check if there's already a pending update request for this service
        $existingPendingRequest = Service::where('original_service_id', $service->id)
            ->where('is_update_request', true)
            ->where('status', 'pending')
            ->first();

        if ($existingPendingRequest) {
            return redirect()->back()
                ->with('error', 'Permohonan kemaskini untuk perkhidmatan ini masih menunggu kelulusan admin. Sila tunggu sehingga permohonan sedia ada diluluskan atau ditolak.')
                ->withInput();
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($service->title !== $validated['title']) {
            $hasChanges = true;
            $changes[] = 'Tajuk perkhidmatan';
        }
        if ($service->description !== $validated['description']) {
            $hasChanges = true;
            $changes[] = 'Penerangan';
        }
        if ($service->location !== $validated['location']) {
            $hasChanges = true;
            $changes[] = 'Lokasi';
        }
        if ($service->pricing !== $validated['pricing']) {
            $hasChanges = true;
            $changes[] = 'Harga';
        }
        if ($service->contact_info !== $validated['contact_info']) {
            $hasChanges = true;
            $changes[] = 'Maklumat hubungan';
        }
        if ($service->category !== $validated['category']) {
            $hasChanges = true;
            $changes[] = 'Kategori';
        }

        // Check tags
        $newTags = $validated['tags'] ? array_map('trim', explode(',', $validated['tags'])) : [];
        $currentTags = is_array($service->tags) ? $service->tags : [];
        if (count(array_diff($newTags, $currentTags)) > 0 || count(array_diff($currentTags, $newTags)) > 0) {
            $hasChanges = true;
            $changes[] = 'Tag';
        }

        // Check images
        $currentImages = $request->input('current_images', []);
        $originalImages = is_array($service->images) ? $service->images : [];
        if (count(array_diff($currentImages, $originalImages)) > 0 || count(array_diff($originalImages, $currentImages)) > 0) {
            $hasChanges = true;
            $changes[] = 'Gambar';
        }

        // Check for new images
        if ($request->hasFile('new_images')) {
            $hasChanges = true;
            $changes[] = 'Gambar baharu';
        }

        // If no changes, show warning
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat sebelum menghantar permohonan.')
                ->withInput();
        }

        // Delete any existing rejected update requests for this service
        Service::where('original_service_id', $service->id)
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->delete();

        // Create a new service record for the update request
        $updateService = new Service();
        $updateService->user_id = auth()->id();
        $updateService->title = $validated['title'];
        
        // Generate unique slug for update request
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug . '-update-' . $service->id . '-' . time();
        
        $updateService->slug = $slug;
        $updateService->description = $validated['description'];
        $updateService->location = $validated['location'];
        $updateService->pricing = $validated['pricing'];
        $updateService->contact_info = $validated['contact_info'];
        $updateService->category = $validated['category'];
        $updateService->tags = $newTags;
        $updateService->status = 'pending';
        $updateService->is_update_request = true; // Flag to identify this is an update request
        $updateService->original_service_id = $service->id; // Reference to original service

        // Handle images
        $newImages = [];
        
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('services', 'public');
                $newImages[] = $path;
            }
        }
        
        $updateService->images = array_merge($currentImages, $newImages);
        $updateService->save();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan telah dihantar! Admin akan menyemak permohonan anda dalam masa 1-3 hari bekerja.');
    }

    public function previewServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        $originalService = Service::find($updateRequest->original_service_id);
        
        if (!$originalService) {
            return redirect()->route('dashboard')->with('error', 'Perkhidmatan asal tidak dijumpai.');
        }

        // Get changes for comparison
        $changes = $this->getServiceChanges($originalService, $updateRequest);

        return view('client.requests.service-update-preview', compact('updateRequest', 'originalService', 'changes'));
    }

    private function getServiceChanges($originalService, $updateService)
    {
        $changes = [];
        
        // Compare basic fields
        if ($originalService->title !== $updateService->title) {
            $changes['title'] = [
                'old' => $originalService->title,
                'new' => $updateService->title
            ];
        }
        
        if ($originalService->description !== $updateService->description) {
            $changes['description'] = [
                'old' => $originalService->description,
                'new' => $updateService->description
            ];
        }
        
        if ($originalService->location !== $updateService->location) {
            $changes['location'] = [
                'old' => $originalService->location,
                'new' => $updateService->location
            ];
        }
        
        if ($originalService->pricing !== $updateService->pricing) {
            $changes['pricing'] = [
                'old' => $originalService->pricing,
                'new' => $updateService->pricing
            ];
        }
        
        if ($originalService->contact_info !== $updateService->contact_info) {
            $changes['contact_info'] = [
                'old' => $originalService->contact_info,
                'new' => $updateService->contact_info
            ];
        }
        
        if ($originalService->category !== $updateService->category) {
            $changes['category'] = [
                'old' => $originalService->category,
                'new' => $updateService->category
            ];
        }
        
        // Compare tags
        $originalTags = is_array($originalService->tags) ? $originalService->tags : [];
        $newTags = is_array($updateService->tags) ? $updateService->tags : [];
        if (count(array_diff($originalTags, $newTags)) > 0 || count(array_diff($newTags, $originalTags)) > 0) {
            $changes['tags'] = [
                'old' => implode(', ', $originalTags),
                'new' => implode(', ', $newTags)
            ];
        }
        
        // Compare images
        $originalImages = is_array($originalService->images) ? $originalService->images : [];
        $newImages = is_array($updateService->images) ? $updateService->images : [];
        if (count(array_diff($originalImages, $newImages)) > 0 || count(array_diff($newImages, $originalImages)) > 0) {
            $changes['images'] = [
                'old_count' => count($originalImages),
                'new_count' => count($newImages),
                'added' => array_diff($newImages, $originalImages),
                'removed' => array_diff($originalImages, $newImages)
            ];
        }
        
        return $changes;
    }

    public function cancelServiceRequest($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('is_update_request', false)
            ->firstOrFail();

        // Delete the service
        $service->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan perkhidmatan telah dibatalkan.');
    }

    public function cancelServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->whereIn('status', ['pending', 'rejected'])
            ->firstOrFail();

        // Delete the update request
        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan telah dibatalkan.');
    }

    public function forgetServiceUpdateRequest($id)
    {
        $updateRequest = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('is_update_request', true)
            ->where('status', 'rejected')
            ->firstOrFail();

        $updateRequest->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan kemaskini perkhidmatan berjaya dilupakan.');
    }

    public function forgetServiceRequest($id)
    {
        $service = Service::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'rejected')
            ->where('is_update_request', false)
            ->firstOrFail();

        $service->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan perkhidmatan berjaya dilupakan.');
    }

    public function previewPendingSellerRequest()
    {
        $user = auth()->user();
        
        if (!$user->seller_status || $user->seller_status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Permohonan penjual tidak ditemui.');
        }

        return view('client.requests.pending-seller-preview', compact('user'));
    }

    public function cancelSellerRequest()
    {
        $user = auth()->user();
        
        if (!$user->seller_status || $user->seller_status !== 'pending') {
            return redirect()->route('dashboard')->with('error', 'Permohonan penjual tidak ditemui.');
        }

        // Delete associated files
        if ($user->id_document) {
            Storage::disk('public')->delete($user->id_document);
        }
        if ($user->selfie_with_id) {
            Storage::disk('public')->delete($user->selfie_with_id);
        }

        // Reset seller status
        $user->update([
            'seller_status' => null,
            'seller_rejection_reason' => null,
            'id_document' => null,
            'selfie_with_id' => null
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan penjual telah dibatalkan.');
    }

    public function previewRejectedSellerRequest()
    {
        $user = auth()->user();
        
        if (!$user->seller_status || $user->seller_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'Permohonan penjual tidak ditemui.');
        }

        return view('client.requests.rejected-seller-preview', compact('user'));
    }

    public function editRejectedSellerRequest()
    {
        $user = auth()->user();
        
        if (!$user->seller_status || $user->seller_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'Permohonan penjual tidak ditemui.');
        }

        return view('client.requests.rejected-seller-edit', compact('user'));
    }

    public function updateRejectedSellerRequest(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->seller_status || $user->seller_status !== 'rejected') {
            return redirect()->route('dashboard')->with('error', 'Permohonan penjual tidak ditemui.');
        }

        // Create validation rules based on business type
        $validationRules = [
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'business_registration' => 'nullable|string|max:255',
            'business_address' => 'required|string|max:500',
            'operating_area' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'years_experience' => 'required|integer|min:0|max:50',
            'skills' => 'required|string|max:1000',
            'service_areas' => 'required|string|max:500',
            'id_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
        
        // Make selfie_with_id conditional based on business_type
        if ($request->business_type !== 'company') {
            $validationRules['selfie_with_id'] = 'required|file|mimes:jpg,jpeg,png|max:2048';
        } else {
            $validationRules['selfie_with_id'] = 'nullable|file|mimes:jpg,jpeg,png|max:2048';
        }
        
        $validated = $request->validate($validationRules);

        // Check if any changes were made
        $hasChanges = false;
        $changes = [];

        // Check basic fields
        if ($user->business_name !== $request->business_name) {
            $hasChanges = true;
            $changes[] = 'Nama perniagaan';
        }
        if ($user->business_type !== $request->business_type) {
            $hasChanges = true;
            $changes[] = 'Jenis perniagaan';
        }
        if ($user->business_registration !== $request->business_registration) {
            $hasChanges = true;
            $changes[] = 'Pendaftaran perniagaan';
        }
        if ($user->business_address !== $request->business_address) {
            $hasChanges = true;
            $changes[] = 'Alamat perniagaan';
        }
        if ($user->operating_area !== $request->operating_area) {
            $hasChanges = true;
            $changes[] = 'Kawasan operasi';
        }
        if ($user->website !== $request->website) {
            $hasChanges = true;
            $changes[] = 'Laman web';
        }
        if ($user->years_experience != $request->years_experience) {
            $hasChanges = true;
            $changes[] = 'Tahun pengalaman';
        }
        if ($user->skills !== $request->skills) {
            $hasChanges = true;
            $changes[] = 'Kemahiran';
        }
        if ($user->service_areas !== $request->service_areas) {
            $hasChanges = true;
            $changes[] = 'Kawasan perkhidmatan';
        }

        // Check for new files
        if ($request->hasFile('id_document')) {
            $hasChanges = true;
            $changes[] = 'Dokumen pengenalan';
        }
        if ($request->hasFile('selfie_with_id')) {
            $hasChanges = true;
            $changes[] = 'Selfie dengan ID';
        }

        // If no changes, show reminder
        if (!$hasChanges) {
            return redirect()->back()
                ->with('warning', 'Tiada perubahan dibuat. Sila kemaskini maklumat berdasarkan sebab penolakan sebelum menghantar semula.')
                ->withInput();
        }

        // Handle file uploads
        if ($request->hasFile('id_document')) {
            // Delete old file if exists
            if ($user->id_document) {
                Storage::disk('public')->delete($user->id_document);
            }
            $validated['id_document'] = $request->file('id_document')->store('seller-documents', 'public');
        }

        if ($request->hasFile('selfie_with_id')) {
            // Delete old file if exists
            if ($user->selfie_with_id) {
                Storage::disk('public')->delete($user->selfie_with_id);
            }
            $validated['selfie_with_id'] = $request->file('selfie_with_id')->store('seller-documents', 'public');
        }

        // Update user with new seller information
        $user->update([
            'business_name' => $validated['business_name'],
            'business_type' => $validated['business_type'],
            'business_registration' => $validated['business_registration'],
            'business_address' => $validated['business_address'],
            'operating_area' => $validated['operating_area'],
            'website' => $validated['website'],
            'years_experience' => $validated['years_experience'],
            'skills' => $validated['skills'],
            'service_areas' => $validated['service_areas'],
            'seller_status' => 'pending', // Reset to pending for new submission
            'seller_rejection_reason' => null, // Clear rejection reason
            'seller_application_date' => now(), // Update application date for resubmission
            'id_document' => $validated['id_document'] ?? $user->id_document,
            'selfie_with_id' => $validated['selfie_with_id'] ?? $user->selfie_with_id
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Permohonan penjual anda telah dihantar semula dan sedang menunggu kelulusan admin!');
    }
} 