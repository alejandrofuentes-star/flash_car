<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderImage extends Model
{
    protected $fillable = ['type', 'image_path', 'orden', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeDesktop($query)
    {
        return $query->where('type', 'desktop');
    }

    public function scopeMobile($query)
    {
        return $query->where('type', 'mobile');
    }
}
