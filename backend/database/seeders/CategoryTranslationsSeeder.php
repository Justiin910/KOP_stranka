<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = base_path('storage/category_translations.json');
        if (!file_exists($file)) {
            $this->command?->warn("Translation file not found: {$file}");
            return;
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $this->command?->error('Invalid JSON in translation file.');
            return;
        }

        foreach ($data as $key => $enLabel) {
            $cat = DB::table('categories')->where('key_name', $key)->first();
            if (!$cat) {
                $this->command?->warn("Category not found: {$key}");
                continue;
            }

            $labels = [];
            if (!empty($cat->labels)) {
                $labels = json_decode($cat->labels, true) ?: [];
            }

            $labels['en'] = $enLabel;
            if (!isset($labels['sk'])) {
                $labels['sk'] = $cat->label;
            }

            DB::table('categories')->where('id', $cat->id)->update([
                'labels' => json_encode($labels),
            ]);

            $this->command?->info("Updated category {$key}");
        }
    }
}
