<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_zone_id',
        'destination_zone_id',
        'price_per_kg',
        'base_price'
    ];

    public function originZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'origin_zone_id');
    }

    public function destinationZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'destination_zone_id');
    }
}
