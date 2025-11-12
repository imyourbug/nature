<?php

namespace Tests\Feature;

use App\Constant\GlobalConstant;
use App\Models\Product;
use App\Models\RFQ;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private const AUTH_HEADER = ['Authorization' => 'Bearer admin-token'];

    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        // Load data from JSON file and seed into the in-memory database
        $jsonPath = database_path('mock_data.json');
        $data = json_decode(File::get($jsonPath), true);

        // Vendors (bulk insert)
        $vendors = array_map(function ($vendor) {
            return [
                'id' => $vendor['id'],
                'name' => $vendor['name'],
                'email' => $vendor['email'],
                'status' => $vendor['status'],
                'verified' => $vendor['verified'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data['vendors'] ?? []);
        if (!empty($vendors)) {
            DB::table('vendors')->insert($vendors);
        }

        // Products (bulk insert)
        $products = array_map(function ($product) {
            return [
                'id' => $product['id'],
                'name' => $product['name'],
                'brand' => $product['brand'],
                'price' => $product['price'],
                'specs' => json_encode($product['specs']),
                'status' => $product['status'],
                'vendor_id' => $product['vendor_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data['products'] ?? []);
        if (!empty($products)) {
            DB::table('products')->insert($products);
        }

        // RFQs (bulk insert)
        $rfqs = array_map(function ($rfq) {
            return [
                'id' => $rfq['id'],
                'product_id' => $rfq['product_id'],
                'vendor_id' => $rfq['vendor_id'],
                'quantity' => $rfq['quantity'],
                'status' => $rfq['status'],
                'created_at' => isset($rfq['created_at']) ? Carbon::parse($rfq['created_at']) : now(),
                'updated_at' => now(),
            ];
        }, $data['rfqs'] ?? []);
        if (!empty($rfqs)) {
            DB::table('rfqs')->insert($rfqs);
        }

        // Keep a reference vendor for later test usage
        $this->vendor = Vendor::first();
    }

    public function test_get_products_returns_expected_format(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'data' => [
                    [
                        'id',
                        'name',
                        'brand',
                        'price',
                        'status',
                        'vendor_id',
                        'vendor',
                    ],
                ]
            ],
        ]);
    }

    public function test_post_rfqs_validation_error_when_quantity_missing(): void
    {
        $payload = [
            'product_id' => Product::first()->id,
        ];

        $response = $this->withHeaders(self::AUTH_HEADER)->postJson('/api/rfqs', $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_put_rfqs_accept_updates_status(): void
    {
        $rfq = RFQ::create([
            'product_id' => Product::first()->id,
            'vendor_id' => $this->vendor->id,
            'quantity' => 5,
            'status' => GlobalConstant::RFQ_STATUS_PENDING,
        ]);

        $response = $this->withHeaders(self::AUTH_HEADER)->putJson("/api/rfqs/{$rfq->id}/accept");

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $rfq->id,
                'status' => GlobalConstant::RFQ_STATUS_ACCEPTED,
            ],
        ]);

        $this->assertDatabaseHas('rfqs', [
            'id' => $rfq->id,
            'status' => GlobalConstant::RFQ_STATUS_ACCEPTED,
        ]);
    }
}
