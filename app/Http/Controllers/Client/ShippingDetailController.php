<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ShippingDetail;
use Illuminate\Http\Request;

class ShippingDetailController extends Controller
{
    public function index()
    {
        $shippingDetails = auth()->user()->shippingDetails()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();
        
        return view('client.shipping-details.index', compact('shippingDetails'));
    }

    public function create()
    {
        return view('client.shipping-details.create');
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

        $shippingDetail = ShippingDetail::create([
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

        if ($request->boolean('is_default')) {
            ShippingDetail::setAsDefault($shippingDetail->id, auth()->id());
        }

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat penghantaran berjaya ditambah!');
    }

    public function edit(ShippingDetail $shippingDetail)
    {
        // Ensure user can only edit their own shipping details
        if ($shippingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        return view('client.shipping-details.edit', compact('shippingDetail'));
    }

    public function update(Request $request, ShippingDetail $shippingDetail)
    {
        // Ensure user can only update their own shipping details
        if ($shippingDetail->user_id !== auth()->id()) {
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

        $shippingDetail->update([
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

        if ($request->boolean('is_default')) {
            ShippingDetail::setAsDefault($shippingDetail->id, auth()->id());
        }

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat penghantaran berjaya dikemas kini!');
    }

    public function destroy(ShippingDetail $shippingDetail)
    {
        // Ensure user can only delete their own shipping details
        if ($shippingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        $shippingDetail->delete();

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat penghantaran berjaya dipadam!');
    }

    public function setDefault(ShippingDetail $shippingDetail)
    {
        // Ensure user can only set default for their own shipping details
        if ($shippingDetail->user_id !== auth()->id()) {
            abort(403);
        }

        ShippingDetail::setAsDefault($shippingDetail->id, auth()->id());

        return redirect()->route('addresses.index')
                        ->with('success', 'Alamat penghantaran lalai berjaya ditetapkan!');
    }
}
