<?php

namespace App\Models;

use App\Enums\StoreType;
use App\Traits\HasHaversineFormula;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static selectRaw(string $string, array $array)
 */
class Store extends Model
{
    use HasFactory, HasHaversineFormula;

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

    public function scopeHaversine(Builder $query, $latitude, $longitude): Builder
    {
        return $query->select('*')
            ->selectRaw("{$this->getFormula($latitude, $longitude)} AS distance")
            ->groupBy('id', 'latitude', 'longitude', 'created_at', 'updated_at', 'name');
    }

    public function scopeWithinDistance(Builder $query, float $latitude, float $longitude, int $distance = 5): Builder
    {
        return $this->scopeHaversine($query, $latitude, $longitude)
            ->having('distance', '<=', $distance)
            ->orderBy('distance');
    }

    public function scopeWithinDeliveryDistance(Builder $query, float $latitude, float $longitude): Builder
    {
        return $this->scopeHaversine($query, $latitude, $longitude)
            ->havingRaw('distance <= max_delivery_distance')
            ->orderBy('distance');
    }
}
