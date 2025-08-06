<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BillingDetail;
use Illuminate\Http\Request;

class BillingDetailController extends Controller
{
    public function index()
    {
        $billingDetails = auth()->user()->billingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        $shippingDetails = auth()->user()->shippingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('client.billing-details.index', compact('billingDetails', 'shippingDetails'));
    }

    public function create()
    {
        return view('client.billing-details.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $billingDetail = BillingDetail::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'label' => $request->label,
            'is_default' => $request->boolean('is_default'),
        ]);

        // If this is set as default, remove default from others
        if ($billingDetail->is_default) {
            BillingDetail::where('user_id', auth()->id())
                        ->where('id', '!=', $billingDetail->id)
                        ->update(['is_default' => false]);
        }

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat bil berjaya ditambah!');
    }

    public function edit(BillingDetail $billingDetail)
    {
        // Ensure user can only edit their own billing details
        if ($billingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.billing-details.edit', compact('billingDetail'));
    }

    public function update(Request $request, BillingDetail $billingDetail)
    {
        // Ensure user can only update their own billing details
        if ($billingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'is_default' => 'boolean',
        ]);

        $billingDetail->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'label' => $request->label,
            'is_default' => $request->boolean('is_default'),
        ]);

        // If this is set as default, remove default from others
        if ($billingDetail->is_default) {
            BillingDetail::where('user_id', auth()->id())
                        ->where('id', '!=', $billingDetail->id)
                        ->update(['is_default' => false]);
        }

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat bil berjaya dikemas kini!');
    }

    public function destroy(BillingDetail $billingDetail)
    {
        // Ensure user can only delete their own billing details
        if ($billingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        $billingDetail->delete();

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat bil berjaya dipadam!');
    }

    public function setDefault(BillingDetail $billingDetail)
    {
        // Ensure user can only set default for their own billing details
        if ($billingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        $billingDetail->setAsDefault();

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat bil telah ditetapkan sebagai lalai!');
    }
}
