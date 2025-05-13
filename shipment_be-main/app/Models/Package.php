<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'sender_id',
        'receiver_id',
        'item_name',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'value',
        'origin_address',
        'destination_address',
        'status',
        'picked_up_at',
        'delivered_at'
    ];
    
    protected $casts = [
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class);
    }

    public function trackingUpdates()
    {
        return $this->hasMany(TrackingUpdate::class);
    }
}
