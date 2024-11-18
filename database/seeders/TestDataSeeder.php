<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Product;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Créer quelques clients de test
        Customer::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'phone' => '0123456789'
        ]);

        Customer::create([
            'name' => 'Marie Martin',
            'email' => 'marie.martin@example.com',
            'phone' => '0987654321'
        ]);

        // Créer quelques produits de test
        Product::create([
            'name' => 'Ordinateur Portable',
            'price' => 999.99,
            'stock' => 10
        ]);

        Product::create([
            'name' => 'Smartphone',
            'price' => 599.99,
            'stock' => 15
        ]);

        Product::create([
            'name' => 'Tablette',
            'price' => 299.99,
            'stock' => 20
        ]);
    }
}
