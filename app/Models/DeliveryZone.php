<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'base_price'
    ];

    public function originPrices()
    {
        return $this->hasMany(Price::class, 'origin_zone_id');
    }

    public function destinationPrices()
    {
        return $this->hasMany(Price::class, 'destination_zone_id');
    }
}
