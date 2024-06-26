<?php

namespace App\Enums;

enum StoreType: string
{
    case Takeaway = 'takeaway';
    case Shop = 'shop';
    case Restaurant = 'restaurant';
}
