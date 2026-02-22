<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class RecommendedProductController extends Controller
{
    public function recommended()
    {
        // Príklad: odporúčame top 6 podľa ratingu
        $products = Product::orderByDesc('rating')->limit(6)->get();
        return response()->json($products);
    }
}
