<?php

namespace Tests\Feature;

use App\Models\Postcode;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreCanDeliverToPostcodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_stores_can_deliver_to_postcode(): void
    {
        // Create a postcode
        Postcode::create([
            'postcode' => 'TS4 3TS',
            'latitude' => 54.544105317071825,
            'longitude' => -1.210862175846583,
        ]);

        // Create stores
        Store::create([
            'name' => 'Test Store 1',
            'latitude' => 54.521172849463404,
            'longitude' => -1.2535098572881425,
            'is_open' => true,
            'store_type' => \App\Enums\StoreType::Shop,
            'max_delivery_distance' => 1, // This store can deliver within 1 km
        ]);

        Store::create([
            'name' => 'Test Store 2',
            'latitude' => 54.5689045407684,
            'longitude' => -1.2230607639330415,
            'is_open' => true,
            'store_type' => \App\Enums\StoreType::Shop,
            'max_delivery_distance' => 10, // This store can deliver within 10 km
        ]);

        // Make the request
        $this->withoutExceptionHandling();
        $response = $this->getJson('/api/stores/can-deliver/TS4 3TS');

        // Assert the response
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    public function test_postcode_not_found(): void
    {
        // Make the request with a non-existent postcode
        $response = $this->getJson('/api/stores/can-deliver/NE12PA');

        // Assert the response
        $response->assertStatus(404);
        $response->assertJson(['message' => 'Postcode not found']);
    }
}
