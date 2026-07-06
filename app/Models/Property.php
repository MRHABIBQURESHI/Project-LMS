<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'price',
        'size',
        'bedrooms',
        'bathrooms',
        'garages',
        'description',
        'image',
        'gallery_images',
        'floor_plan_images',
        'property_type_id',
        'city_id',
        'project_id',
        'developer_id',
        'is_featured',
        'status',
        'air_conditioning',
        'alarm',
        'balcony',
        'cable_tv',
        'central_heating',
        'dryer',
        'dishwasher',
        'garage',
        'gym',
        'library',
        'laundry_room',
        'microwave',
        'oven',
        'parking',
        'pets_allowed',
        'refrigerator',
        'security_system',
        'swimming_pool',
        'tennis_court',
        'tv_cable',
        'wifi',
        'washer',
        'wine_cellar',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'size' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'garages' => 'integer',
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'gallery_images' => 'array',
        'floor_plan_images' => 'array',
        'air_conditioning' => 'boolean',
        'alarm' => 'boolean',
        'balcony' => 'boolean',
        'cable_tv' => 'boolean',
        'central_heating' => 'boolean',
        'dryer' => 'boolean',
        'dishwasher' => 'boolean',
        'garage' => 'boolean',
        'gym' => 'boolean',
        'library' => 'boolean',
        'laundry_room' => 'boolean',
        'microwave' => 'boolean',
        'oven' => 'boolean',
        'parking' => 'boolean',
        'pets_allowed' => 'boolean',
        'refrigerator' => 'boolean',
        'security_system' => 'boolean',
        'swimming_pool' => 'boolean',
        'tennis_court' => 'boolean',
        'tv_cable' => 'boolean',
        'wifi' => 'boolean',
        'washer' => 'boolean',
        'wine_cellar' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($property) {
            if (empty($property->slug)) {
                $property->slug = static::generateUniqueSlug($property->name, $property->id);
            }
        });
    }

    private static function generateUniqueSlug($title, $id = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Property Type relationship.
     */
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    /**
     * City relationship.
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    /**
     * Project relationship.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Developer relationship.
     */
    public function developerRelation()
    {
        return $this->belongsTo(Developer::class, 'developer_id');
    }

    /**
     * Format price helper.
     */
    public function getFormattedPriceAttribute()
    {
        $currency = 'PKR';
        if ($this->city && strtolower($this->city->country) === 'uae') {
            $currency = 'AED';
        }
        return $currency . ' ' . number_format((float) $this->price, 0);
    }
}
