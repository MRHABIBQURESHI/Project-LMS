<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'meta_description' => 'Dreams Propertys is a premium real estate agency.',
                'meta_keywords' => 'real estate, property, dubai, pakistan, buy house',
                'phone_number' => ['+971 55 316 5168', '+971 55 316 5168'],
                'email_address' => ['info@dreamspropertys.com', 'safzalhayder@dreamspropertys.com'],
                'address' => 'Office No 1407, Bay Square, Business Bay, Dubai, Dubai, United Arab Emirates',
                'facebook' => 'https://facebook.com/dreamspropertys',
                'instagram' => 'https://instagram.com/dreamspropertys',
                'youtube' => 'https://youtube.com/dreamspropertys',
                'twitter' => 'https://twitter.com/dreamspropertys',
                'linkedin' => 'https://linkedin.com/company/dreamspropertys',
                'whatsapp' => 'https://wa.me/971553165168',
                'working_hours' => 'Mon - Sat: 10:00 AM - 6:00 PM',
                'footer_description' => 'Dreams Propertys offers the finest selection of luxury properties, off-plan projects, and residential developments across the Middle East and South Asia.',
                'logo_black' => 'assets/images/logo/logo.svg',
                'logo_white' => 'assets/images/logo/logo-white.png',
                'fav_icon' => 'assets/images/favicon.png',
            ]
        );
    }
}
