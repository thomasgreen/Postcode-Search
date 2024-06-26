<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class StoresController extends Controller
{
    public function store(StoreRequest $request): JsonResponse
    {
        $store = Store::create($request->validated());

        return response()->json(['store' => $store], 201);
    }
}
