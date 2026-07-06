<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'city_id',
        'developer_id',
        'image',
        'banner_image',
        'bannger_image',
        'description',
        'location',
        'completion_year',
        'starting_price',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'starting_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($project) {
            if (empty($project->slug)) {
                $project->slug = static::generateUniqueSlug($project->name, $project->id);
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
     * City this project belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Properties in this project.
     */
    public function properties()
    {
        return $this->hasMany(Property::class)->where('status', true);
    }
    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class)->where('status', true);
    }

    /**
     * Developer this project belongs to.
     */
    public function developerRelation()
    {
        return $this->belongsTo(Developer::class, 'developer_id');
    }

    /**
     * Accessor for developer to return string name (backward compatibility with views).
     */
    public function getDeveloperAttribute()
    {
        return $this->developerRelation ? $this->developerRelation->name : null;
    }

    /**
     * Accessor to return banner_image (spelling compatibility with views).
     */
    public function getBanngerImageAttribute()
    {
        return $this->banner_image;
    }

    /**
     * Formatted starting price.
     */
    public function getFormattedStartingPriceAttribute()
    {
        $currency = 'PKR';
        if ($this->city && strtolower($this->city->country) === 'uae') {
            $currency = 'AED';
        }
        return $currency . ' ' . number_format((float) $this->starting_price, 0);
    }
}
