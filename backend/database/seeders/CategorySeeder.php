<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'id' => 1,
                'key_name' => 'phones',
                'label' => 'Mobily, smartfóny',
                'icon' => 'fa-solid fa-mobile-screen-button',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 2,
                'key_name' => 'tablets',
                'label' => 'Tablety',
                'icon' => 'fa-solid fa-tablet-screen-button',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 3,
                'key_name' => 'notebooks',
                'label' => 'Počítače a notebooky',
                'icon' => 'fa-solid fa-laptop',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 4,
                'key_name' => 'pcs',
                'label' => 'Herné počítače',
                'icon' => 'fa-solid fa-desktop',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 5,
                'key_name' => 'powerbanks',
                'label' => 'Powerbanky a nabíjačky',
                'icon' => 'fa-solid fa-battery-three-quarters',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
            [
                'id' => 6,
                'key_name' => 'sluchadla',
                'label' => 'Slúchadlá a audio',
                'icon' => 'fa-solid fa-headphones',
                'created_at' => NULL,
                'updated_at' => NULL,
            ],
        ]);
    }
}
