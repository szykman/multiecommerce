<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRule;
use Illuminate\Http\Request;

class ShippingRuleController extends Controller
{
    protected array $states = [
        'AC','AL','AP','AM','BA','CE','DF','ES','GO',
        'MA','MT','MS','MG','PA','PB','PR','PE','PI',
        'RJ','RN','RS','RO','RR','SC','SP','SE','TO',
    ];

    public function index()
    {
        $rules = ShippingRule::where('store_id', auth()->user()->store_id)
            ->orderBy('position')
            ->get();

        return view('admin.shipping_rules.index', compact('rules'));
    }

    public function create()
    {
        $states = $this->states;

        return view('admin.shipping_rules.create', compact('states'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['store_id'] = auth()->user()->store_id;

        ShippingRule::create($data);

        return redirect()
            ->route('shipping-rules.index')
            ->with('success', 'Regra de frete criada.');
    }

    public function edit(ShippingRule $shippingRule)
    {
        abort_if($shippingRule->store_id != auth()->user()->store_id, 403);

        $states = $this->states;

        return view('admin.shipping_rules.edit', [
            'rule' => $shippingRule,
            'states' => $states,
        ]);
    }

    public function update(Request $request, ShippingRule $shippingRule)
    {
        abort_if($shippingRule->store_id != auth()->user()->store_id, 403);

        $data = $this->validateData($request);

        $shippingRule->update($data);

        return redirect()
            ->route('shipping-rules.index')
            ->with('success', 'Regra de frete atualizada.');
    }

    public function destroy(ShippingRule $shippingRule)
    {
        abort_if($shippingRule->store_id != auth()->user()->store_id, 403);

        $shippingRule->delete();

        return back()->with('success', 'Regra de frete removida.');
    }

    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:pickup,region',
            'states' => 'nullable|array',
            'states.*' => 'string|size:2',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        // Retirada não depende de região
        if ($data['type'] === 'pickup') {
            $data['states'] = null;
        }

        $data['active'] = $request->boolean('active');
        $data['min_weight'] = $data['min_weight'] ?? 0;
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }
}
