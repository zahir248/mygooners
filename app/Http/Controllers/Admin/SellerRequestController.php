<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SellerRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('seller_status', '!=', null)
                    ->where('seller_status', '!=', 'pending')
                    ->withCount('services');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('seller_status')) {
            $query->where('seller_status', $request->seller_status);
        }

        // Order by latest first (handle null values)
        $query->orderByRaw('COALESCE(seller_application_date, created_at) DESC');

        // Get paginated results
        $sellers = $query->paginate(15);

        return view('admin.seller-requests.index', compact('sellers'));
    }

    public function pending()
    {
        $sellers = User::where('seller_status', 'pending')
            ->withCount('services')
            ->orderByRaw('COALESCE(seller_application_date, created_at) DESC')
            ->paginate(15);

        return view('admin.seller-requests.pending', compact('sellers'));
    }

    public function show($id)
    {
        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        
        return view('admin.seller-requests.show', compact('seller'));
    }

    public function getServices($id)
    {
        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        $services = $seller->services()->select('id', 'title', 'description', 'pricing', 'status', 'created_at')->get();
        
        return response()->json([
            'services' => $services
        ]);
    }

    public function approve($id)
    {
        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        
        $seller->update([
            'is_seller' => true,
            'seller_status' => 'approved',
            'is_verified' => true
        ]);

        return redirect()->route('admin.seller-requests.pending')
            ->with('success', 'Permohonan penjual diluluskan dengan jayanya!');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'seller_rejection_reason' => 'required|string|max:1000'
        ]);

        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        
        $seller->update([
            'seller_status' => 'rejected',
            'seller_rejection_reason' => $request->seller_rejection_reason
        ]);

        return redirect()->route('admin.seller-requests.pending')
            ->with('success', 'Permohonan penjual ditolak dengan jayanya!');
    }

    public function toggleStatus($id)
    {
        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        
        if ($seller->seller_status === 'approved') {
            $seller->update([
                'seller_status' => 'rejected',
                'is_seller' => false
            ]);
            $message = 'Status penjual telah ditolak!';
        } else {
            $seller->update([
                'seller_status' => 'approved',
                'is_seller' => true
            ]);
            $message = 'Status penjual telah diluluskan!';
        }

        return redirect()->route('admin.seller-requests.index')
            ->with('success', $message);
    }

    public function destroy($id)
    {
        $seller = User::where('seller_status', '!=', null)->findOrFail($id);
        
        // Delete associated files
        if ($seller->id_document) {
            \Storage::disk('public')->delete($seller->id_document);
        }
        if ($seller->selfie_with_id) {
            \Storage::disk('public')->delete($seller->selfie_with_id);
        }
        
        // Reset seller status
        $seller->update([
            'is_seller' => false,
            'seller_status' => null,
            'seller_rejection_reason' => null
        ]);

        return redirect()->route('admin.seller-requests.index')
            ->with('success', 'Permohonan penjual dipadam dengan jayanya!');
    }
}
