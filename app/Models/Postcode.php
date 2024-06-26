<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'postcode',
        'latitude',
        'longitude',
    ];

    protected $primaryKey = 'postcode';
}
