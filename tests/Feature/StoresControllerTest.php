<?php

namespace Tests\Feature;

use App\Enums\StoreType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoresControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_store_creation(): void
    {
        $data = [
            'name' => 'Test Store',
            'latitude' => 45.123456,
            'longitude' => -93.123456,
            'is_open' => true,
            'store_type' => StoreType::Shop->value,
            'max_delivery_distance' => 10,
        ];

        $response = $this->postJson(route('addStore'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stores', $data);
    }

    public function test_store_creation_validation(): void
    {
        $data = [
            'name' => 'Test Store',
            'latitude' => 100, // Invalid latitude
            'longitude' => -93.123456,
            'is_open' => true,
            'store_type' => StoreType::Shop->value,
            'max_delivery_distance' => 10,
        ];

        $response = $this->postJson(route('addStore'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('latitude');
    }
}
