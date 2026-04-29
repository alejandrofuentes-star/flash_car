<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'active'];

    public function deliveryPoints()
    {
        return $this->hasMany(DeliveryPoint::class)->orderBy('name');
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class);
    }
}
