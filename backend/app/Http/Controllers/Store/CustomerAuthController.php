<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerAuthController extends Controller
{
    public function registerForm()
    {
        return view('store.auth.register');
    }

    public function register(Request $request, TenantManager $tenant)
    {
        $storeId = $tenant->id();

        $data = $request->validate([

            'name' => 'required|string|max:255',

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers')->where(
                    fn ($query) => $query->where('store_id', $storeId)
                ),
            ],

            'phone' => 'nullable|string|max:30',

            'password' => 'required|string|min:6|confirmed',

        ]);

        $customer = Customer::create([
            'store_id' => $storeId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Auth::guard('customer')->login($customer);

        $request->session()->regenerate();

        return redirect()
            ->route('store.account')
            ->with('success', 'Cadastro realizado com sucesso!');
    }

    public function loginForm()
    {
        return view('store.auth.login');
    }

    public function login(Request $request, TenantManager $tenant)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->boolean('remember');

        $attempted = Auth::guard('customer')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'store_id' => $tenant->id(),
            'active' => true,
        ], $remember);

        if ($attempted) {

            $request->session()->regenerate();

            return redirect()->intended(route('store.account'));
        }

        return back()
            ->withErrors(['email' => 'E-mail ou senha inválidos.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.login');
    }

    public function account()
    {
        $customer = Auth::guard('customer')->user();

        return view('store.auth.account', compact('customer'));
    }
}
