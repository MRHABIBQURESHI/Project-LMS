<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'city_id',
        'developer_id',
        'property_type_id',
        'project_id',
        'name',
        'slug',
        'designation',
        'description',
        'image',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'whatsapp',
        'other_link',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($agent) {
            if (empty($agent->slug) && !empty($agent->name)) {
                $agent->slug = static::generateUniqueSlug($agent->name, $agent->id);
            }
        });
    }

    private static function generateUniqueSlug($name, $id = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
                ->where('id', '!=', $id)
                ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}