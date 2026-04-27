<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Toy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────
        User::create(['name' => 'Administrator', 'email' => 'admin@toyvault.com',  'password' => Hash::make('password'), 'role' => 'admin',  'is_active' => true]);
        User::create(['name' => 'Staff Toko',    'email' => 'staff@toyvault.com',  'password' => Hash::make('password'), 'role' => 'staff',  'is_active' => true]);
        User::create(['name' => 'Viewer Laporan','email' => 'viewer@toyvault.com', 'password' => Hash::make('password'), 'role' => 'viewer', 'is_active' => true]);

        // ── Categories ─────────────────────────────────────
        $categories = [
            ['name' => 'Action Figure',   'color' => '#ff6b35'],
            ['name' => 'Puzzle',          'color' => '#ffd166'],
            ['name' => 'Diecast / Mobil', 'color' => '#06d6a0'],
            ['name' => 'Boneka',          'color' => '#ef4565'],
            ['name' => 'Board Game',      'color' => '#6366f1'],
            ['name' => 'Lego / Brick',    'color' => '#f72585'],
            ['name' => 'Edukatif',        'color' => '#4cc9f0'],
        ];
        foreach ($categories as $cat) { Category::create($cat); }

        // ── Toys ───────────────────────────────────────────
        $toys = [
            ['name' => 'Hot Wheels Lamborghini Huracán', 'brand' => 'Mattel',       'condition' => 'new',      'buy_price' => 35000,   'sell_price' => 55000,   'stock' => 12, 'category_id' => 3, 'age_range' => '3-12', 'barcode' => '89912345001'],
            ['name' => 'LEGO Star Wars Millennium Falcon','brand' => 'LEGO',         'condition' => 'new',      'buy_price' => 1200000, 'sell_price' => 1650000, 'stock' => 3,  'category_id' => 6, 'age_range' => '14+',  'barcode' => '89912345002'],
            ['name' => 'Puzzle 1000 pcs Pemandangan',    'brand' => 'Ravensburger', 'condition' => 'good',     'buy_price' => 180000,  'sell_price' => 280000,  'stock' => 7,  'category_id' => 2, 'age_range' => '12+',  'barcode' => '89912345003'],
            ['name' => 'Barbie Malibu Doll',             'brand' => 'Mattel',       'condition' => 'new',      'buy_price' => 220000,  'sell_price' => 350000,  'stock' => 5,  'category_id' => 4, 'age_range' => '3-12', 'barcode' => '89912345004'],
            ['name' => 'Monopoly Indonesia',             'brand' => 'Hasbro',       'condition' => 'like_new', 'buy_price' => 250000,  'sell_price' => 380000,  'stock' => 4,  'category_id' => 5, 'age_range' => '8+',   'barcode' => '89912345005'],
            ['name' => 'Spider-Man Action Figure 30cm',  'brand' => 'Marvel',       'condition' => 'new',      'buy_price' => 350000,  'sell_price' => 550000,  'stock' => 2,  'category_id' => 1, 'age_range' => '4-12', 'barcode' => '89912345006'],
            ['name' => 'Mainan Kayu Sorting Shape',      'brand' => 'Local',        'condition' => 'new',      'buy_price' => 75000,   'sell_price' => 130000,  'stock' => 15, 'category_id' => 7, 'age_range' => '1-3',  'barcode' => '89912345007'],
            ['name' => 'Tamiya Mini 4WD Avante',         'brand' => 'Tamiya',       'condition' => 'good',     'buy_price' => 150000,  'sell_price' => 230000,  'stock' => 1,  'category_id' => 3, 'age_range' => '8+',   'barcode' => '89912345008'],
        ];
        foreach ($toys as $data) { Toy::create($data); }
    }
}
