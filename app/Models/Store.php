<?php

namespace App\Models;

use App\Enums\StoreType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $casts = [
        'store_type' => StoreType::class,
    ];

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'is_open',
        'store_type',
        'max_delivery_distance'
    ];
}
