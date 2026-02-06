<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProdukKategori;
use App\Models\Produk;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        User::create([
            'nama_user' => 'Owner Toko',
            'username_user' => 'owner',
            'password_user' => Hash::make('owner123'),
            'role_user' => 'owner',
        ]);

        User::create([
            'nama_user' => 'Kasir 1',
            'username_user' => 'kasir',
            'password_user' => Hash::make('kasir123'),
            'role_user' => 'kasir',
        ]);

        // Create Kategori
        $kategoriMakanan = ProdukKategori::create(['nama_kategori' => 'Makanan']);
        $kategoriMinuman = ProdukKategori::create(['nama_kategori' => 'Minuman']);
        $kategoriKebutuhan = ProdukKategori::create(['nama_kategori' => 'Kebutuhan Rumah Tangga']);

        // Create Sample Products
        $products = [
            ['nama_produk' => 'Indomie Goreng', 'harga_produk' => 3500, 'stock_produk' => 50, 'code_produk' => 'IDM001', 'kategori' => $kategoriMakanan],
            ['nama_produk' => 'Beras 5kg', 'harga_produk' => 65000, 'stock_produk' => 20, 'code_produk' => 'BRS001', 'kategori' => $kategoriMakanan],
            ['nama_produk' => 'Aqua 600ml', 'harga_produk' => 3000, 'stock_produk' => 100, 'code_produk' => 'AQU001', 'kategori' => $kategoriMinuman],
            ['nama_produk' => 'Teh Pucuk', 'harga_produk' => 4000, 'stock_produk' => 75, 'code_produk' => 'TEA001', 'kategori' => $kategoriMinuman],
            ['nama_produk' => 'Sabun Mandi Lifebuoy', 'harga_produk' => 8500, 'stock_produk' => 30, 'code_produk' => 'SBN001', 'kategori' => $kategoriKebutuhan],
            ['nama_produk' => 'Shampo Pantene', 'harga_produk' => 15000, 'stock_produk' => 25, 'code_produk' => 'SHP001', 'kategori' => $kategoriKebutuhan],
        ];

        foreach ($products as $product) {
            Produk::create([
                'nama_produk' => $product['nama_produk'],
                'harga_produk' => $product['harga_produk'],
                'stock_produk' => $product['stock_produk'],
                'code_produk' => $product['code_produk'],
                'id_produk_kategori' => $product['kategori']->id_produk_kategori,
            ]);
        }
    }
}
