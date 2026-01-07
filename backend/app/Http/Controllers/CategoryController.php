<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'sk');

        $categories = DB::table('categories')->get();

        // Map categories to prefer localized label from `labels` JSON column when available
        $mapped = $categories->map(function ($cat) use ($lang) {
            $labels = null;
            if (isset($cat->labels)) {
                try {
                    $labels = json_decode($cat->labels, true);
                } catch (\Throwable $e) {
                    $labels = null;
                }
            }

            $label = $cat->label;
            if (is_array($labels) && isset($labels[$lang]) && !empty($labels[$lang])) {
                $label = $labels[$lang];
            }

            return [
                'id' => $cat->id,
                'key_name' => $cat->key_name,
                'label' => $label,
                'labels' => $labels,
                'icon' => $cat->icon,
            ];
        });

        return response()->json($mapped);
    }
}