<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['order', 'user', 'images']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        

        // Search by order number or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('order', function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $statusCounts = [
            'pending' => Refund::where('status', 'pending')->count(),
            'approved' => Refund::where('status', 'approved')->count(),
            'rejected' => Refund::where('status', 'rejected')->count(),
            'processing' => Refund::where('status', 'processing')->count(),
            'completed' => Refund::where('status', 'completed')->count(),
        ];

        return view('admin.refunds.index', compact('refunds', 'statusCounts'));
    }

    public function show(Refund $refund)
    {
        $refund->load(['order', 'user', 'images']);
        return view('admin.refunds.show', compact('refund'));
    }

    public function updateStatus(Request $request, Refund $refund)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,processing,completed',
            'admin_notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'receipt_image' => 'required_if:status,completed|nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'status.required' => 'Status diperlukan.',
            'status.in' => 'Status tidak sah.',
            'admin_notes.max' => 'Nota admin tidak boleh melebihi 1000 aksara.',
            'rejection_reason.required_if' => 'Sebab penolakan diperlukan apabila status ditolak.',
            'rejection_reason.max' => 'Sebab penolakan tidak boleh melebihi 1000 aksara.',
            'receipt_image.required_if' => 'Resit transaksi refund diperlukan apabila status selesai.',
            'receipt_image.image' => 'Fail mesti dalam format gambar.',
            'receipt_image.mimes' => 'Format gambar yang diterima: JPEG, PNG, JPG.',
            'receipt_image.max' => 'Saiz gambar tidak boleh melebihi 2MB.',
        ]);

        try {
            DB::beginTransaction();

            $oldStatus = $refund->status;
            
            // Handle receipt image upload if status is completed
            $receiptImagePath = null;
            if ($request->status === 'completed' && $request->hasFile('receipt_image')) {
                $image = $request->file('receipt_image');
                $filename = 'receipt_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $receiptImagePath = $image->storeAs('refunds/' . $refund->id . '/receipts', $filename, 'public');
            }
            
            $refund->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'rejection_reason' => $request->rejection_reason,
                'receipt_image' => $receiptImagePath,
            ]);

            // If status is completed, update order status to refunded
            if ($request->status === 'completed') {
                $refund->order->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded'
                ]);
                $refund->update(['refunded_at' => now()]);
            }

            // If status was completed and now changed, revert order status
            if ($oldStatus === 'completed' && $request->status !== 'completed') {
                $refund->order->update([
                    'status' => 'delivered',
                    'payment_status' => 'paid'
                ]);
                $refund->update(['refunded_at' => null]);
            }

            DB::commit();

            $statusMessages = [
                'approved' => 'Permohonan refund telah diluluskan.',
                'rejected' => 'Permohonan refund telah ditolak.',
                'processing' => 'Status refund telah diubah kepada sedang diproses.',
                'completed' => 'Refund telah selesai dan status pesanan telah dikemas kini.',
                'pending' => 'Status refund telah dikembalikan kepada menunggu.',
            ];

            return back()->with('success', $statusMessages[$request->status] ?? 'Status refund telah dikemas kini.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ralat berlaku semasa mengemas kini status refund.');
        }
    }

    public function export(Request $request)
    {
        $query = Refund::with(['order', 'user']);

                            // Apply filters
                    if ($request->filled('status')) {
                        $query->where('status', $request->status);
                    }

        $refunds = $query->orderBy('created_at', 'desc')->get();

        $filename = 'refunds_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($refunds) {
            $file = fopen('php://output', 'w');
            
                                    // CSV headers
                        fputcsv($file, [
                            'ID Refund',
                            'Nombor Pesanan',
                            'Nama Pengguna',
                            'Email Pengguna',
                            'Sebab Refund',
                            'Jumlah Refund',
                            'Status',
                            'Bank',
                            'Nombor Akaun',
                            'Nama Pemegang Akaun',
                            'Tracking Number',
                            'Courier',
                            'Tarikh Permohonan',
                            'Nota Admin',
                            'Sebab Penolakan',
                            'Tarikh Selesai'
                        ]);

                                    foreach ($refunds as $refund) {
                            fputcsv($file, [
                                $refund->id,
                                $refund->order->order_number,
                                $refund->user->name,
                                $refund->user->email,
                                $refund->refund_reason,
                                $refund->getFormattedRefundAmount(),
                                $refund->getStatusDisplayName(),
                                $refund->bank_name,
                                $refund->bank_account_number,
                                $refund->bank_account_holder,
                                $refund->tracking_number ?? 'N/A',
                                $refund->shipping_courier ?? 'N/A',
                                $refund->created_at->format('d/m/Y H:i'),
                                $refund->admin_notes ?? 'N/A',
                                $refund->rejection_reason ?? 'N/A',
                                $refund->refunded_at ? $refund->refunded_at->format('d/m/Y H:i') : 'N/A'
                            ]);
                        }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getStats()
    {
        $totalRefunds = Refund::count();
        $pendingRefunds = Refund::where('status', 'pending')->count();
        $approvedRefunds = Refund::where('status', 'approved')->count();
        $completedRefunds = Refund::where('status', 'completed')->count();
        $rejectedRefunds = Refund::where('status', 'rejected')->count();
        
        $totalRefundAmount = Refund::where('status', 'completed')->sum('refund_amount');
        $pendingRefundAmount = Refund::where('status', 'pending')->sum('refund_amount');
        
        

                            return response()->json([
                        'total_refunds' => $totalRefunds,
                        'pending_refunds' => $pendingRefunds,
                        'approved_refunds' => $approvedRefunds,
                        'completed_refunds' => $completedRefunds,
                        'rejected_refunds' => $rejectedRefunds,
                        'total_refund_amount' => number_format($totalRefundAmount, 2),
                        'pending_refund_amount' => number_format($pendingRefundAmount, 2),
                    ]);
    }
} 