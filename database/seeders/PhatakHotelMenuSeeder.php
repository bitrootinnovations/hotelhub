<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhatakHotelMenuSeeder extends Seeder
{
    public function run()
    {
        $client = DB::table('client_masters')
            ->where('client_name', 'Hotel Phatak')
            ->first();

        if (!$client) {
            $this->command->error('Client "Phatak Hotel" not found.');
            return;
        }

        $category = DB::table('category_masters')
            ->where('client_id', $client->client_id)
            ->where('status_id', 1)
            ->whereRaw('LOWER(category_name) LIKE ?', ['%non%veg%'])
            ->first();

        if (!$category) {
            $this->command->error('Non-veg category not found for Phatak Hotel.');
            return;
        }

        $this->command->info("Client: {$client->client_name} (ID: {$client->client_id})");
        $this->command->info("Category: {$category->category_name} (ID: {$category->category_id})");

        $items = [
            ['menu_name' => 'Mutton Fry',        'price' => 250.00],
            ['menu_name' => 'Mutton Ukkad',       'price' => 350.00],
            ['menu_name' => 'Mutton Aalani Fry',  'price' => 260.00],
            ['menu_name' => 'Mutton Masala',      'price' => 300.00],
            ['menu_name' => 'Mutton Kharda',      'price' => 280.00],
            ['menu_name' => 'Mutton Handi Full',  'price' => 700.00],
            ['menu_name' => 'Mutton Handi Half',  'price' => 400.00],
            ['menu_name' => 'Chicken Fry',        'price' => 180.00],
            ['menu_name' => 'Chicken Ukkad',      'price' => 250.00],
            ['menu_name' => 'Chicken Aalani Fry', 'price' => 180.00],
            ['menu_name' => 'Chicken Masala',     'price' => 220.00],
            ['menu_name' => 'Chicken Kharda',     'price' => 200.00],
            ['menu_name' => 'Chicken Handi Full', 'price' => 550.00],
            ['menu_name' => 'Chicken Handi Half', 'price' => 300.00],
            ['menu_name' => 'Egg Burji',          'price' => 100.00],
            ['menu_name' => 'Egg Omlet',          'price' => 100.00],
            ['menu_name' => 'Egg Boil',           'price' =>  50.00],
        ];

        $now = now();

        foreach ($items as $item) {
            $exists = DB::table('menu_masters')
                ->where('menu_name', $item['menu_name'])
                ->where('client_id', $client->client_id)
                ->exists();

            if ($exists) {
                $this->command->warn("  SKIP (already exists): {$item['menu_name']}");
                continue;
            }

            DB::table('menu_masters')->insert([
                'menu_name'      => $item['menu_name'],
                'category_id'    => $category->category_id,
                'food_type'      => 2,
                'price'          => $item['price'],
                'gst_percentage' => 0,
                'stock_type'     => 'Unit',
                'quantity'       => null,
                'client_id'      => $client->client_id,
                'status_id'      => 1,
                'created_by'     => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $this->command->info("  Added: {$item['menu_name']} @ ₹{$item['price']}");
        }

        $this->command->info('Done.');
    }
}
