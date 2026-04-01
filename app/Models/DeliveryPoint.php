<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPoint extends Model
{
    protected $fillable = ['state_id', 'name', 'address', 'active'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
