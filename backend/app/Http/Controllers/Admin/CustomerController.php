<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Rules\ValidCpfCnpj;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::where(
                'store_id',
                auth()->user()->store_id
            )
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%')
                      ->orWhere('email', 'like', '%'.$request->search.'%');
                });
            })
            ->withCount('orders')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        abort_if(
            $customer->store_id != auth()->user()->store_id,
            403
        );

        $customer->load(['addresses', 'orders' => function ($q) {
            $q->latest();
        }]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * O lojista pode editar o CPF/CNPJ do cliente (ex: cliente pediu
     * ajuda por telefone/WhatsApp) — sem isso, boleto fica travado
     * até o próprio cliente preencher pela área dele.
     */
    public function update(Request $request, Customer $customer)
    {
        abort_if(
            $customer->store_id != auth()->user()->store_id,
            403
        );

        $data = $request->validate([
            'document' => ['required', 'string', new ValidCpfCnpj()],
        ]);

        $customer->update([
            'document' => preg_replace('/\D/', '', $data['document']),
        ]);

        return back()->with('success', 'CPF/CNPJ do cliente atualizado.');
    }
}
