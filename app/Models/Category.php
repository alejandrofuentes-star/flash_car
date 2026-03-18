<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name', 'slug', 'price_per_day', 'price_per_week',
        'price_per_month', 'warranty', 'image_url', 'active',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'price_per_day'   => 'decimal:2',
        'price_per_week'  => 'decimal:2',
        'price_per_month' => 'decimal:2',
        'warranty'        => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $slug = Str::slug($category->name);
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = Str::slug($category->name) . '-' . $count++;
                }
                $category->slug = $slug;
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $slug = Str::slug($category->name);
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = Str::slug($category->name) . '-' . $count++;
                }
                $category->slug = $slug;
            }
        });
    }

    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeInactive($query) { return $query->where('active', false); }

    public function getFormattedPricePerDayAttribute() { return '$' . number_format($this->price_per_day, 2); }
    public function getFormattedPricePerWeekAttribute() { return '$' . number_format($this->price_per_week, 2); }
    public function getFormattedPricePerMonthAttribute() { return '$' . number_format($this->price_per_month, 2); }
    public function getFormattedWarrantyAttribute() { return '$' . number_format($this->warranty, 2); }

    public function isActive() { return $this->active === true; }
    public function isInactive() { return $this->active === false; }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}