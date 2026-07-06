<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'Karachi',
                'slug' => 'karachi',
                'state' => 'Sindh',
                'country' => 'Pakistan',
                'image' => 'assets/images/cities/image1.png',
                'banner_image' => 'assets/images/cities/image1.png',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Lahore',
                'slug' => 'lahore',
                'state' => 'Punjab',
                'country' => 'Pakistan',
                'image' => 'assets/images/cities/image2.png',
                'banner_image' => 'assets/images/cities/image2.png',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Islamabad',
                'slug' => 'islamabad',
                'state' => 'ICT',
                'country' => 'Pakistan',
                'image' => 'assets/images/cities/image3.png',
                'banner_image' => 'assets/images/cities/image3.png',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Dubai',
                'slug' => 'dubai',
                'state' => 'Dubai',
                'country' => 'UAE',
                'image' => 'assets/images/cities/image4.png',
                'banner_image' => 'assets/images/cities/image4.png',
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Abu Dhabi',
                'slug' => 'abu-dhabi',
                'state' => 'Abu Dhabi',
                'country' => 'UAE',
                'image' => 'assets/images/cities/image1.png',
                'banner_image' => 'assets/images/cities/image1.png',
                'is_featured' => true,
                'status' => true,
            ],
        ];

        foreach ($cities as $cityData) {
            City::updateOrCreate(
                ['slug' => $cityData['slug']],
                $cityData
            );
        }
    }
}
