<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class B2BSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đọc file mock_data.json
        $jsonPath = database_path('mock_data.json');

        if (!File::exists($jsonPath)) {
            $this->command->error('File mock_data.json not found!');
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        // Seed Vendors
        if (isset($data['vendors'])) {
            foreach ($data['vendors'] as $vendor) {
                DB::table('vendors')->insert([
                    'id' => $vendor['id'],
                    'name' => $vendor['name'],
                    'email' => $vendor['email'],
                    'status' => $vendor['status'],
                    'verified' => $vendor['verified'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('Seeded ' . count($data['vendors']) . ' vendors');
        }

        // Seed Products
        if (isset($data['products'])) {
            foreach ($data['products'] as $product) {
                DB::table('products')->insert([
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'brand' => $product['brand'],
                    'price' => $product['price'],
                    'specs' => json_encode($product['specs']),
                    'status' => $product['status'],
                    'vendor_id' => $product['vendor_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('Seeded ' . count($data['products']) . ' products');
        }

        // Seed RFQs
        if (isset($data['rfqs'])) {
            foreach ($data['rfqs'] as $rfq) {
                DB::table('rfqs')->insert([
                    'id' => $rfq['id'],
                    'product_id' => $rfq['product_id'],
                    'vendor_id' => $rfq['vendor_id'],
                    'quantity' => $rfq['quantity'],
                    'status' => $rfq['status'],
                    'created_at' => isset($rfq['created_at'])
                        ? Carbon::parse($rfq['created_at'])
                        : now(),
                    'updated_at' => now(),
                ]);
            }
            $this->command->info('Seeded ' . count($data['rfqs']) . ' RFQs');
        }

        $this->command->info('B2B data seeded successfully!');
    }
}
