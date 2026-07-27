<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $addresses = Address::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->get();

        return view('store.auth.addresses', compact('addresses'));
    }

    public function destroy(Address $address)
    {
        $customer = Auth::guard('customer')->user();

        abort_if($address->customer_id !== $customer->id, 403);

        $wasDefault = $address->is_default;

        $address->delete();

        // Se apagou o endereço padrão e ainda sobrou algum, promove
        // o mais recente a padrão, pra sempre haver um marcado.
        if ($wasDefault) {

            $next = Address::where('customer_id', $customer->id)->first();

            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Endereço removido.');
    }
}
