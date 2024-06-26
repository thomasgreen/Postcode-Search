<?php

namespace App\Traits;

trait HasHaversineFormula
{
    protected function getFormula(float $latitude, float $longitude): string
    {
        return "(6371 * acos(cos(radians($latitude))
                        * cos(radians(latitude))
                        * cos(radians(longitude) - radians($longitude))
                        + sin(radians($latitude))
                        * sin(radians(latitude))))";
    }
}
