<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'box_ids_json',
        'category_image',
        'is_active',
        'featured',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'box_ids_json' => 'array'
    ];


    // ✅ Relationships
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ✅ Many-to-many relationship with box
    public function boxes()
    {
        return $this->belongsToMany(Box::class, 'category_box'); // <- correct pivot table name
    }

    // ✅ Accessors
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

    // ✅ Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ✅ Business Logic
    public function syncBoxes(array $boxIds)
    {
        $this->update(['box_ids_json' => $boxIds]);
        $this->boxes()->sync($boxIds);
        return $this;
    }

    public function deleteWithCleanup()
    {
        if ($this->subcategories()->exists() || $this->products()->exists()) {
            return false;
        }

        if ($this->category_image) {
            Storage::disk('public')->delete($this->category_image);
        }

        $this->boxes()->detach();

        return $this->delete();
    }
}
