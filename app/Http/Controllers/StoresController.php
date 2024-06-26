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

    public function storesNearPostcode(Request $request, string $postcode): JsonResponse
    {
        // Find the postcode entry
        $postcodeEntry = Postcode::where('postcode', $postcode)->first();

        if (!$postcodeEntry) {
            return response()->json(['message' => 'Postcode not found'], 404);
        }

        // Get latitude and longitude
        $latitude = $postcodeEntry->latitude;
        $longitude = $postcodeEntry->longitude;

        // Default radius in kilometers
        $distance = $request->input('distance', 10);

        // Query to find nearby stores
        $stores = Store::withinDistance($latitude, $longitude, $distance)
            ->paginate(10);

        return response()->json($stores);
    }


    public function storesCanDeliverToPostcode(Request $request, $postcode): JsonResponse
    {
        // Find the postcode entry
        $postcodeEntry = Postcode::where('postcode', $postcode)->first();

        if (!$postcodeEntry) {
            return response()->json(['message' => 'Postcode not found'], 404);
        }

        // Get latitude and longitude
        $latitude = $postcodeEntry->latitude;
        $longitude = $postcodeEntry->longitude;

        // Default radius in kilometers
        $distance = $request->input('distance', 10);

        // Use the WithinDistance scope
        $stores = Store::withinDeliveryDistance($latitude, $longitude, $distance)->paginate(10);

        return response()->json($stores);
    }
}
