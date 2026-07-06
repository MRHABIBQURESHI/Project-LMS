<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class City extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'banner_image',
        'state',
        'country',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($city) {
            if (empty($city->slug)) {
                $city->slug = static::generateUniqueSlug($city->name, $city->id);
            }
        });
    }

    private static function generateUniqueSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Projects in this city.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Properties in this city.
     */
    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Count of active properties in this city.
     */
    public function getPropertiesCountAttribute()
    {
        return $this->properties()->count();
    }
}
