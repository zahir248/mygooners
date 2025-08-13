<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RefundController extends Controller
{
    public function create(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if order can request refund
        if (!$order->canRequestRefund()) {
            return redirect()->route('checkout.orders')->with('error', 'Pesanan ini tidak boleh memohon refund.');
        }

        return view('client.refunds.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if order can request refund
        if (!$order->canRequestRefund()) {
            return redirect()->route('checkout.orders')->with('error', 'Pesanan ini tidak boleh memohon refund.');
        }

        $request->validate([
            'refund_reason' => 'required|string|min:10|max:1000',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'refund_reason.required' => 'Sebab refund diperlukan.',
            'refund_reason.min' => 'Sebab refund mesti sekurang-kurangnya 10 aksara.',
            'refund_reason.max' => 'Sebab refund tidak boleh melebihi 1000 aksara.',
            'images.*.required' => 'Sila muat naik 3 gambar sebagai bukti.',
            'images.*.image' => 'Fail mesti dalam format gambar.',
            'images.*.mimes' => 'Format gambar yang diterima: JPEG, PNG, JPG.',
            'images.*.max' => 'Saiz gambar tidak boleh melebihi 2MB.',
        ]);

        // Check if exactly 3 images are uploaded
        if (!$request->hasFile('images') || count($request->file('images')) !== 3) {
            return back()->withErrors(['images' => 'Sila muat naik tepat 3 gambar sebagai bukti.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Create refund record
            $refund = Refund::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'refund_reason' => $request->refund_reason,
                'refund_amount' => $order->total,
                'status' => 'pending',
            ]);

            // Handle image uploads
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('refunds/' . $refund->id, $filename, 'public');

                RefundImage::create([
                    'refund_id' => $refund->id,
                    'image_path' => $path,
                    'image_name' => $image->getClientOriginalName(),
                    'image_type' => $image->getMimeType(),
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return redirect()->route('checkout.orders')->with('success', 'Permohonan refund anda telah berjaya dihantar. Admin akan menyemak permohonan anda dalam masa 24-48 jam.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Refund creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Ralat berlaku semasa menghantar permohonan refund: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Refund $refund)
    {
        // Check if user owns this refund
        if ($refund->user_id !== auth()->id()) {
            abort(403);
        }

        // Load the order relationship to ensure it's available
        $refund->load('order');

        return view('client.refunds.show', compact('refund'));
    }

    public function index()
    {
        $refunds = auth()->user()->refunds()->with(['order', 'images'])->orderBy('created_at', 'desc')->paginate(10);
        
        return view('client.refunds.index', compact('refunds'));
    }

    public function update(Request $request, Refund $refund)
    {
        // Check if user owns this refund
        if ($refund->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow updates for approved refunds
        if ($refund->status !== 'approved') {
            return back()->with('error', 'Hanya refund yang diluluskan boleh dikemas kini.');
        }

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'bank_account_holder' => 'required|string|max:255',
            'tracking_number' => 'required|string|max:255',
            'shipping_courier' => 'required|string|max:255',
        ], [
            'bank_name.required' => 'Nama bank diperlukan.',
            'bank_account_number.required' => 'Nombor akaun bank diperlukan.',
            'bank_account_holder.required' => 'Nama pemegang akaun diperlukan.',
            'tracking_number.required' => 'Nombor tracking diperlukan.',
            'shipping_courier.required' => 'Syarikat penghantaran diperlukan.',
        ]);

        try {
            $refund->update([
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
                'tracking_number' => $request->tracking_number,
                'shipping_courier' => $request->shipping_courier,
            ]);

            // Check if all required information is now complete
            if ($refund->bank_name && $refund->bank_account_number && $refund->bank_account_holder && $refund->tracking_number && $refund->shipping_courier) {
                // Update status to processing since all information is complete
                $refund->update(['status' => 'processing']);
                return back()->with('success', 'Maklumat refund anda telah lengkap dan status telah diubah kepada "Sedang Diproses". Refund akan diproses dalam masa 3-5 hari bekerja.');
            } else {
                return back()->with('success', 'Maklumat refund anda telah berjaya dikemas kini. Sila lengkapkan semua maklumat untuk memulakan proses refund.');
            }

        } catch (\Exception $e) {
            \Log::error('Refund update error: ' . $e->getMessage());
            return back()->with('error', 'Ralat berlaku semasa mengemas kini maklumat refund: ' . $e->getMessage())->withInput();
        }
    }
} 