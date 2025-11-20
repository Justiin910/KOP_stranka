<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecommendedProductController extends Controller
{
    public function recommended()
    {
        // Príklad: odporúčame top 6 podľa ratingu
        $products = DB::table('products')->orderByDesc('rating')->limit(6)->get();
        return response()->json($products);
    }
}
