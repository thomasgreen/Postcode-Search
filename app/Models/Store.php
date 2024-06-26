<?php

namespace App\Models;

use App\Enums\StoreType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static selectRaw(string $string, array $array)
 */
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
        'max_delivery_distance',
    ];

    public function scopeWithinDistance(Builder $query, float $latitude, float $longitude, int $distance = 5): Builder
    {
        $haversine = "(6371 * acos(cos(radians($latitude))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians($longitude))
                        + sin(radians($latitude))
                        * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->groupBy('id', 'latitude', 'longitude', 'created_at', 'updated_at', 'name')
            ->having('distance', '<=', $distance)
            ->orderBy('distance');
    }

    public function scopeWithinDeliveryDistance(Builder $query, float $latitude, float $longitude): Builder
    {
        $haversine = "(6371 * acos(cos(radians($latitude))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians($longitude))
                        + sin(radians($latitude))
                        * sin(radians(latitude))))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance")
            ->groupBy('id', 'latitude', 'longitude', 'created_at', 'updated_at', 'name')
            ->havingRaw('distance <= max_delivery_distance')
            ->orderBy('distance');
    }
}
