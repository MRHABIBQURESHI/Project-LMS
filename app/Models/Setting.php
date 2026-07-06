<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'meta_description',
        'meta_keywords',
        'phone_number',
        'email_address',
        'address',
        'facebook',
        'instagram',
        'youtube',
        'twitter',
        'linkedin',
        'whatsapp',
        'working_hours',
        'footer_description',
        'logo_black',
        'logo_white',
        'fav_icon',
        'updated_by'
    ];

    protected $casts = [
        'phone_number' => 'array',
        'email_address' => 'array',
    ];

    public function getWhiteLogoAttribute()
    {
        return $this->logo_white;
    }

    public function getBlackLogoAttribute()
    {
        return $this->logo_black;
    }

    public function getFacebookUrlAttribute()
    {
        return $this->facebook;
    }

    public function getTwitterUrlAttribute()
    {
        return $this->twitter;
    }

    public function getInstagramUrlAttribute()
    {
        return $this->instagram;
    }

    public function getLinkedinUrlAttribute()
    {
        return $this->linkedin;
    }
}
