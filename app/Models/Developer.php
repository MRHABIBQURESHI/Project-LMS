<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Developer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'website_url',
        'number',
        'email',
        'slug',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Automatically generate slug from name.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($developer) {
            if (empty($developer->slug) && !empty($developer->name)) {
                $developer->slug = static::generateUniqueSlug($developer->name);
            }
        });

        static::updating(function ($developer) {
            if (
                $developer->isDirty('name') &&
                empty($developer->slug)
            ) {
                $developer->slug = static::generateUniqueSlug($developer->name, $developer->id);
            }
        });
    }

    /**
     * Generate a unique slug.
     */
    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (
            static::when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }
}