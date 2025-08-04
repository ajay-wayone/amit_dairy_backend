<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_category',
        'box_ids_json',
        'category_image',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'box_ids_json' => 'array'
    ];

    protected $appends = ['image_url'];

    // Relationships
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function boxes()
    {
        return $this->belongsToMany(Box::class);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->category_image 
            ? asset('storage/' . $this->category_image) 
            : null;
    }

    public function getBoxIdsAttribute()
    {
        return $this->box_ids_json ?? [];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Business Logic
    public function syncBoxes(array $boxIds)
    {
        $this->update(['box_ids_json' => $boxIds]);
        $this->boxes()->sync($boxIds);
        return $this;
    }

    public function deleteWithCleanup()
    {
        // Check for dependencies
        if ($this->subcategories()->exists() || $this->products()->exists()) {
            return false;
        }

        // Delete image file
        if ($this->category_image) {
            Storage::disk('public')->delete($this->category_image);
        }

        // Detach boxes
        $this->boxes()->detach();

        return $this->delete();
    }
}