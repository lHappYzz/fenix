<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Відеокарта RX 6700 XT',
                'price' => 16430,
                'image' => asset('images/rx6700.png'),
                'description' => <<<EOT
                    Відеокарта AMD RADEON RX 6700 XT 12GB створена для тих, хто прагне до високої продуктивності
                    в геймінгу та прагне отримати максимум від ігрового процесу.
                    Забезпечуючи відмінну производительність у роздільній здатності 1440p, ця карта є ідеальним вибором
                    для сучасних геймерів, що цінують деталізовану графіку та плавність зображення.
                EOT,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Відеокарта Sapphire PCI-Ex Radeon RX 7700 XT Pure Frostpunk 2',
                'price' => 21777,
                'image' => asset('images/rx7700.png'),
                'description' => <<<EOT
                    Відеокарта AMD Radeon RX 7700 XT в стилі Frostpunk 2 стане ідеальним вибором для
                    довгоочікуваної відеоігри від 11-бітних студій.
                    Відображаючи теми гри, відеокарта SAPPHIRE має морозно-білий кожух вентилятора та масляно-чорну металеву задню панель
                    із плямами іржі.
                EOT,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Відеокарта Sapphire PCI-Ex Radeon RX 7900 XT 20GB GDDR6',
                'price' => 35338,
                'image' => asset('images/rx7900.png'),
                'description' => <<<EOT
                    Випробуйте неперевершену продуктивність, візуальні ефекти й ефективність
                    у роздільній здатності 4K і вище з відеокартами Radeon RX 7000 Series - першими у світі ігровими
                    графічними процесорами на базі технології AMD RDNA 3.
                EOT,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
