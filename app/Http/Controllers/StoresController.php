<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoresController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $store = Store::create($request->validated());

        return response()->json(['store' => $store], 201);
    }

    public function storesNearPostcode(Request $request, Postcode $postcode): JsonResponse
    {
        // Get latitude and longitude
        $latitude = $postcode->latitude;
        $longitude = $postcode->longitude;

        // Default radius in kilometers
        $distance = $request->input('distance', 10);

        // Query to find nearby stores
        $stores = Store::withinDistance($latitude, $longitude, $distance)
            ->paginate(10);

        return response()->json($stores);
    }

    public function storesCanDeliverToPostcode(Postcode $postcode): JsonResponse
    {
        // Get latitude and longitude
        $latitude = $postcode->latitude;
        $longitude = $postcode->longitude;

        // Use the WithinDistance scope
        $stores = Store::withinDeliveryDistance($latitude, $longitude)->paginate(10);

        return response()->json($stores);
    }
}
