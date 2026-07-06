<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name'        => 'Villa',
                'slug'        => 'villa',
                'icon'        => 'fa-home',
                'description' => 'Luxurious standalone villas with premium amenities and private gardens.',
                'is_featured' => true,
                'status'      => true,
            ],
            [
                'name'        => 'Apartment',
                'slug'        => 'apartment',
                'icon'        => 'fa-building',
                'description' => 'Modern apartments in urban areas with all facilities.',
                'is_featured' => true,
                'status'      => true,
            ],
            [
                'name'        => 'Studio',
                'slug'        => 'studio',
                'icon'        => 'fa-bed',
                'description' => 'Compact studios perfect for singles and young professionals.',
                'is_featured' => true,
                'status'      => true,
            ],
            [
                'name'        => 'Penthouse',
                'slug'        => 'penthouse',
                'icon'        => 'fa-star',
                'description' => 'Top-floor luxury penthouses with panoramic city views.',
                'is_featured' => true,
                'status'      => true,
            ],
            [
                'name'        => 'House',
                'slug'        => 'house',
                'icon'        => 'fa-house-chimney',
                'description' => 'Family homes in suburban neighborhoods with spacious yards.',
                'is_featured' => true,
                'status'      => true,
            ],
            [
                'name'        => 'Office',
                'slug'        => 'office',
                'icon'        => 'fa-briefcase',
                'description' => 'Commercial office spaces in prime business districts.',
                'is_featured' => false,
                'status'      => true,
            ],
            [
                'name'        => 'Co-living',
                'slug'        => 'co-living',
                'icon'        => 'fa-people-roof',
                'description' => 'Shared co-living spaces with community amenities.',
                'is_featured' => false,
                'status'      => true,
            ],
        ];

        foreach ($types as $type) {
            PropertyType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
