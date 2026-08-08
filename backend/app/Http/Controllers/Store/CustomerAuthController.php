<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Rules\ValidCpfCnpj;
use App\Services\TenantManager;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

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

            'phone' => [
                'required',
                'string',
                'max:30',
                function ($attribute, $value, $fail) {

                    if ($value && str_starts_with($value, '+55')) {

                        if (! preg_match('/^\+55 \d{2} 9\d{4}-\d{4}$/', $value)) {
                            $fail('Informe um celular brasileiro válido, no formato (DD) 9XXXX-XXXX.');
                        }
                    }
                },
            ],

            // Obrigatório desde o cadastro: boleto (Mercado Pago) exige
            // o CPF/CNPJ do pagador pra emitir a cobrança, e vai servir
            // também pra nota fiscal futuramente.
            'document' => ['required', 'string', new ValidCpfCnpj()],

            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->numbers(),
            ],

        ]);

        $customer = Customer::create([
            'store_id' => $storeId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'document' => preg_replace('/\D/', '', $data['document']),
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

        $addresses = \App\Models\Address::where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->get();

        return view('store.auth.account', compact('customer', 'addresses'));
    }

    public function updateAccount(\Illuminate\Http\Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'document' => ['required', 'string', new ValidCpfCnpj()],
        ]);

        $data['document'] = preg_replace('/\D/', '', $data['document']);

        $customer->update($data);

        return back()->with('success', 'Dados atualizados com sucesso.');
    }


    /*
    |--------------------------------------------------------------------------
    | Esqueci minha senha
    |--------------------------------------------------------------------------
    */

    public function forgotPasswordForm()
    {
        return view('store.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Enviamos um link de redefinição para o seu e-mail.')
            : back()->withErrors(['email' => 'Não encontramos um cliente com esse e-mail nesta loja.']);
    }

    public function resetPasswordForm(Request $request, string $token)
    {
        return view('store.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'confirmed',
                PasswordRule::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Customer $customer, string $password) {

                $customer->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $customer->save();

                event(new PasswordReset($customer));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('store.login')->with('success', 'Senha redefinida com sucesso! Faça login.')
            : back()->withErrors(['email' => 'Token inválido ou expirado. Solicite um novo link.']);
    }
}
