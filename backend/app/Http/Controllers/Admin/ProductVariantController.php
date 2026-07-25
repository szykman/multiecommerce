<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Recebe a lista de opções (ex: Cor, Tamanho) com seus valores
     * (ex: Preto/Branco, P/M/G), sincroniza no banco e gera/atualiza
     * as combinações (variantes) automaticamente.
     *
     * Combinações já existentes (mesmo conjunto de valores) são
     * preservadas com seu preço/estoque/SKU atuais. Combinações que
     * deixaram de existir (porque uma opção ou valor foi removido)
     * são apagadas. Combinações novas são criadas com preço herdado
     * do produto e estoque zerado.
     */
    public function generateOptions(Request $request, Product $product)
    {
        abort_if(
            $product->store_id != auth()->user()->store_id,
            403
        );

        $data = $request->validate([
            'options' => 'required|array|min:1',
            'options.*.name' => 'required|string|max:100',
            'options.*.values' => 'required|array|min:1',
            'options.*.values.*' => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($data, $product) {

            $incomingNames = collect($data['options'])->pluck('name');

            // remove opções que não vieram mais (cascade apaga os
            // valores e os pivôs de variante ligados a eles)
            $product->options()
                ->whereNotIn('name', $incomingNames)
                ->delete();

            $optionValueIdsByOption = [];

            foreach ($data['options'] as $position => $optionData) {

                $option = $product->options()->updateOrCreate(
                    ['name' => $optionData['name']],
                    ['position' => $position]
                );

                $incomingValues = collect($optionData['values']);

                // remove valores que não vieram mais nesta opção
                $option->values()
                    ->whereNotIn('value', $incomingValues)
                    ->delete();

                $valueIds = [];

                foreach ($optionData['values'] as $vPosition => $value) {

                    $optionValue = $option->values()->updateOrCreate(
                        ['value' => $value],
                        ['position' => $vPosition]
                    );

                    $valueIds[] = $optionValue->id;
                }

                $optionValueIdsByOption[] = $valueIds;
            }

            // Produto cartesiano: todas as combinações possíveis
            // entre os valores de cada opção.
            $combinations = $this->cartesianProduct($optionValueIdsByOption);

            // Ativa a flag ANTES de criar/apagar variantes: o hook de
            // sincronização de estoque em ProductVariant (saved/deleted)
            // só recalcula o estoque do produto quando has_variants já
            // está true — se ligássemos isso só no final, a primeira
            // geração deixaria o estoque desatualizado até o próximo
            // salvamento de variante.
            $product->update(['has_variants' => true]);

            $existingVariants = $product->variants()
                ->with('optionValues')
                ->get();

            $existingBySignature = $existingVariants->keyBy(function ($variant) {
                return $variant->optionValues
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->implode('-');
            });

            $newSignatures = collect($combinations)->map(function ($combo) {
                return collect($combo)->sort()->values()->implode('-');
            });

            // apaga variantes cuja combinação não existe mais
            foreach ($existingBySignature as $signature => $variant) {
                if (! $newSignatures->contains($signature)) {
                    $variant->delete();
                }
            }

            // cria variantes novas para combinações que ainda não existem
            foreach ($combinations as $combo) {

                $signature = collect($combo)->sort()->values()->implode('-');

                if ($existingBySignature->has($signature)) {
                    continue;
                }

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'stock' => 0,
                    'active' => true,
                ]);

                $variant->optionValues()->sync($combo);
            }
        });

        $product->refresh();

        return response()->json([
            'success' => true,
            'variants' => $product->variants()
                ->with('optionValues.option')
                ->get()
                ->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
                        'stock' => $variant->stock,
                        'active' => $variant->active,
                        'option_values' => $variant->optionValues->map(function ($ov) {
                            return [
                                'id' => $ov->id,
                                'value' => $ov->value,
                                'option_name' => $ov->option->name,
                            ];
                        }),
                    ];
                }),
            'stock' => $product->stock,
        ]);
    }

    /**
     * Atualiza preço/estoque/SKU/status de uma ou mais variantes
     * de uma vez (a tabela de variantes na blade salva tudo junto).
     */
    public function updateVariants(Request $request, Product $product)
    {
        abort_if(
            $product->store_id != auth()->user()->store_id,
            403
        );

        $data = $request->validate([
            'variants' => 'required|array',
            'variants.*.id' => 'required|integer',
            'variants.*.sku' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.active' => 'nullable|boolean',
        ]);

        foreach ($data['variants'] as $variantData) {

            // Confirma que a variante pertence mesmo a este produto
            // (e, por extensão, a esta loja) antes de atualizar.
            $variant = ProductVariant::where('id', $variantData['id'])
                ->where('product_id', $product->id)
                ->first();

            if (! $variant) {
                continue;
            }

            $variant->update([
                'sku' => $variantData['sku'] ?? null,
                'price' => $variantData['price'],
                'sale_price' => $variantData['sale_price'] ?? null,
                'stock' => $variantData['stock'],
                'active' => $variantData['active'] ?? true,
            ]);
        }

        $product->refresh();

        return response()->json([
            'success' => true,
            'stock' => $product->stock,
        ]);
    }

    /**
     * Remove uma variante específica.
     */
    public function destroy(ProductVariant $variant)
    {
        abort_if(
            $variant->product->store_id != auth()->user()->store_id,
            403
        );

        $product = $variant->product;

        $variant->delete();

        $product->refresh();

        return response()->json([
            'success' => true,
            'stock' => $product->stock,
        ]);
    }

    /**
     * Desliga o modo variação do produto. As opções/variantes
     * continuam no banco (não apaga histórico), só o produto volta
     * a usar o campo "stock" manual em vez da soma automática.
     */
    public function disable(Product $product)
    {
        abort_if(
            $product->store_id != auth()->user()->store_id,
            403
        );

        $product->update(['has_variants' => false]);

        return response()->json(['success' => true]);
    }

    /**
     * Gera todas as combinações possíveis (produto cartesiano) entre
     * os valores de cada opção.
     *
     * Ex: [[1,2], [10,11]] -> [[1,10], [1,11], [2,10], [2,11]]
     */
    protected function cartesianProduct(array $arrays): array
    {
        $result = [[]];

        foreach ($arrays as $propertyValues) {

            $tmp = [];

            foreach ($result as $resultItem) {
                foreach ($propertyValues as $value) {
                    $tmp[] = array_merge($resultItem, [$value]);
                }
            }

            $result = $tmp;
        }

        return $result;
    }
}
