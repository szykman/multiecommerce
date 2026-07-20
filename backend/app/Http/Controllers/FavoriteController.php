<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class FavoriteController extends Controller
{
public function toggle(Product $product)
{

  //dd('cheguei', $product->id);

    $favorites = session()->get('favorites', []);

    if (in_array($product->id, $favorites)) {

        $favorites = array_values(
            array_diff($favorites, [$product->id])
        );

        $favorite = false;

    } else {

        $favorites[] = $product->id;

        $favorite = true;
    }

    session()->put('favorites', $favorites);

 // (session()->all());

    return response()->json([
        'favorite' => $favorite,
        'count'    => count($favorites)
    ]);
}}
