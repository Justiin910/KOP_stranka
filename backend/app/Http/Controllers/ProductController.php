<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function byCategory($category)
    {
        $products = DB::table('products')->where('category', $category)->get();
        return response()->json($products);
    }

}
