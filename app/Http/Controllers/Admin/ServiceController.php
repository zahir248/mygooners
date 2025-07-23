<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('user')->where('status', '!=', 'pending');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Verified filter
        if ($request->has('verified') && $request->verified) {
            $query->where('is_verified', true);
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);

        $categories = Service::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function pending()
    {
        $services = Service::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.services.pending', compact('services'));
    }

    public function create()
    {
        $categories = [
            'Coaching' => 'Coaching',
            'Transport' => 'Pengangkutan',
            'Authentication' => 'Pengesahan',
            'Photography' => 'Rafi',
            'Entertainment' => 'Hiburan',
            'Catering' => 'Katering',
            'Security' => 'Keselamatan',
            'Other' => 'Lain-lain'
        ];

        $users = User::where('role', 'user')->get();

        return view('admin.services.create', compact('categories', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'is_verified' => 'boolean',
            'trust_score' => 'nullable|numeric|between:0,5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $service = new Service();
        $service->user_id = $request->user_id;
        $service->title = $request->title;
        $service->slug = Str::slug($request->title);
        $service->description = $request->description;
        $service->location = $request->location;
        $service->pricing = $request->pricing;
        $service->contact_info = $request->contact_info;
        $service->category = $request->category;
        $service->tags = $request->tags ? explode(',', $request->tags) : [];
        $service->status = 'pending'; // Automatically set to pending
        $service->is_verified = $request->has('is_verified');
        // Ensure trust_score is between 0 and 5
        $trustScore = $request->trust_score ?? 0;
        $service->trust_score = max(0, min(5, floatval($trustScore)));
        $service->views_count = 0;

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = 'services/' . time() . '_' . Str::slug($request->title) . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
                $stored = Storage::disk('public')->putFileAs(
                    dirname($filename),
                    $image,
                    basename($filename)
                );
                if ($stored) {
                    $images[] = $filename;
                }
            }
        }
        $service->images = $images;

        $service->save();

        return redirect()->route('admin.services.index')
            ->with('success', 'Perkhidmatan berjaya dicipta!');
    }

    public function show($id)
    {
        $service = Service::with(['user', 'reviews'])->findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        
        $categories = [
            'Coaching' => 'Coaching',
            'Transport' => 'Pengangkutan',
            'Authentication' => 'Pengesahan',
            'Photography' => 'Rafi',
            'Entertainment' => 'Hiburan',
            'Catering' => 'Katering',
            'Security' => 'Keselamatan',
            'Other' => 'Lain-lain'
        ];

        $users = User::where('role', 'user')->get();

        return view('admin.services.edit', compact('service', 'categories', 'users'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'pricing' => 'required|string|max:255',
            'contact_info' => 'required|string',
            'category' => 'required|string|max:255',
            'tags' => 'nullable|string',
            'status' => 'required|in:active,pending,inactive',
            'is_verified' => 'boolean',
            'trust_score' => 'nullable|numeric|between:0,5',
            'current_images' => 'nullable|array',
            'current_images.*' => 'string',
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $service->user_id = $request->user_id;
        $service->title = $request->title;
        $service->slug = Str::slug($request->title);
        $service->description = $request->description;
        $service->location = $request->location;
        $service->pricing = $request->pricing;
        $service->contact_info = $request->contact_info;
        $service->category = $request->category;
        $service->tags = $request->tags ? explode(',', $request->tags) : [];
        $service->status = $request->status;
        $service->is_verified = $request->has('is_verified');
        // Ensure trust_score is between 0 and 5
        $trustScore = $request->trust_score ?? 0;
        $service->trust_score = max(0, min(5, floatval($trustScore)));

        // Handle image management
        $currentImages = $request->input('current_images', []);
        $newImages = [];
        
        // Process new uploaded images
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
        
        // Combine current and new images (current_images maintains the order from frontend)
        $service->images = array_merge($currentImages, $newImages);
        
        // Delete removed images from storage
        $originalImages = $service->getOriginal('images') ?? [];
        $removedImages = array_diff($originalImages, $currentImages);
        foreach ($removedImages as $removedImage) {
            Storage::disk('public')->delete($removedImage);
        }

        $service->save();

        return redirect()->route('admin.services.index')
            ->with('success', 'Perkhidmatan berjaya dikemaskini!');
    }

    public function approve($id)
    {
        $service = Service::findOrFail($id);
        
        // Check if this is an update request
        if ($service->is_update_request && $service->original_service_id) {
            // Find the original service
            $originalService = Service::find($service->original_service_id);
            
            if ($originalService) {
                // Generate proper slug for the original service
                $baseSlug = Str::slug($service->title);
                $originalSlug = $baseSlug;
                $counter = 1;
                
                // Ensure unique slug
                while (Service::where('slug', $baseSlug)->where('id', '!=', $originalService->id)->exists()) {
                    $baseSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                
                // Update the original service with new data
                $originalService->title = $service->title;
                $originalService->slug = $baseSlug;
                $originalService->description = $service->description;
                $originalService->location = $service->location;
                $originalService->pricing = $service->pricing;
                $originalService->contact_info = $service->contact_info;
                $originalService->category = $service->category;
                $originalService->tags = $service->tags;
                $originalService->images = $service->images;
                $originalService->save();
                
                // Delete the update request
                $service->delete();
                
                return redirect()->route('admin.services.pending')
                    ->with('success', 'Kemaskini perkhidmatan berjaya diluluskan!');
            }
        }
        
        // Regular service approval
        $service->update([
            'is_verified' => true,
            'status' => 'active'
        ]);

        return redirect()->route('admin.services.pending')
            ->with('success', 'Perkhidmatan diluluskan dengan jayanya!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $service = Service::findOrFail($id);
        
        // Check if this is an update request
        if ($service->is_update_request && $service->original_service_id) {
            $service->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);
            
            return redirect()->route('admin.services.pending')
                ->with('success', 'Kemaskini perkhidmatan berjaya ditolak!');
        }
        
        // Regular service rejection
        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->route('admin.services.pending')
            ->with('success', 'Perkhidmatan ditolak dengan jayanya!');
    }

    public function toggleStatus($id)
    {
        $service = Service::findOrFail($id);
        
        if ($service->status === 'active') {
            $service->update(['status' => 'inactive']);
            $message = 'Perkhidmatan telah dinyahaktifkan!';
        } else {
            $service->update(['status' => 'active']);
            $message = 'Perkhidmatan telah diaktifkan!';
        }

        return redirect()->route('admin.services.index')
            ->with('success', $message);
    }

    public function updateStatus(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:active,inactive,pending'
        ]);
        
        $service->update(['status' => $request->status]);
        
        $statusMessages = [
            'active' => 'Perkhidmatan telah diaktifkan!',
            'inactive' => 'Perkhidmatan telah dinyahaktifkan!',
            'pending' => 'Perkhidmatan telah ditetapkan sebagai menunggu!'
        ];

        return redirect()->route('admin.services.index')
            ->with('success', $statusMessages[$request->status]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        
        // Delete associated images
        if ($service->images) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Perkhidmatan dipadam dengan jayanya!');
    }

    public function getServiceDetails($id)
    {
        $service = Service::with('user')->findOrFail($id);
        
        $response = [
            'service' => $service,
            'user' => $service->user,
            'created_at' => $service->created_at->format('d F Y, H:i'),
            'images' => $service->images ? array_map(function($image) {
                return $image ? Storage::url($image) : null;
            }, $service->images) : []
        ];
        
        // If this is an update request, include original service data for comparison
        if ($service->is_update_request && $service->original_service_id) {
            $originalService = Service::find($service->original_service_id);
            if ($originalService) {
                $response['original_service'] = $originalService;
                $response['original_service']['images'] = $originalService->images ? array_map(function($image) {
                    return $image ? Storage::url($image) : null;
                }, $originalService->images) : [];
                $response['changes'] = $this->getServiceChanges($originalService, $service);
            }
        }
        
        return response()->json($response);
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
} 