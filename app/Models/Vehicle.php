<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Vehicle extends Model
{
    protected $fillable = [
        'name', 'category_id', 'slug', 'image_path', 'passengers',
        'fuel_capacity', 'brand', 'model', 'year', 'plate_number',
        'transmission', 'available', 'active',
    ];

    protected $casts = [
        'passengers'    => 'integer',
        'year'          => 'integer',
        'fuel_capacity' => 'decimal:2',
        'available'     => 'boolean',
        'active'        => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicle) {
            if (empty($vehicle->slug)) {
                $baseSlug = Str::slug($vehicle->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $vehicle->slug = $slug;
            }
        });

        static::updating(function ($vehicle) {
            if ($vehicle->isDirty('name') && !$vehicle->isDirty('slug')) {
                $baseSlug = Str::slug($vehicle->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $vehicle->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $vehicle->slug = $slug;
            }
        });

        static::deleting(function ($vehicle) {
            if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
        });
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function scopeActive($query) { return $query->where('active', true); }
    public function scopeAvailable($query) { return $query->where('available', true); }
    public function isAvailable() { return $this->available === true; }
    public function isActive() { return $this->active === true; }

    public function getImageUrlAttribute()
    {
        if ($this->image_path) return Storage::url($this->image_path);
        return null;
    }

    public function hasImage()
    {
        return $this->image_path && Storage::disk('public')->exists($this->image_path);
    }

    public function getFullNameAttribute()
    {
        return $this->brand && $this->model && $this->year
            ? "{$this->brand} {$this->model} {$this->year}"
            : $this->name;
    }

    public function getFormattedFuelCapacityAttribute()
    {
        return number_format($this->fuel_capacity, 1) . ' L';
    }
}