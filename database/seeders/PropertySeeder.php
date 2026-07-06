<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::pluck('id', 'slug');
        $projects = Project::pluck('id', 'slug');
        $propertyTypes = PropertyType::pluck('id', 'slug');

        $gallery_images = [
            'assets/images/properties-details/gallery/01.png',
            'assets/images/properties-details/gallery/03.png',
            'assets/images/properties-details/gallery/07.png',
            'assets/images/properties-details/gallery/05.png'
        ];
        $floor_plan_images = [
            'assets/images/floor-plan/floor1.png',
            'assets/images/floor-plan/floor3.png',
        ];

        $properties = [
            // ─── Ocean Front Residences (Karachi) ───
            [
                'name' => 'Ocean Front 1-Bed Apartment',
                'slug' => 'ocean-front-1-bed-apartment',
                'address' => 'Emaar District, Phase 8 DHA, Karachi',
                'price' => 18000000.00,
                'size' => 850,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'garages' => 1,
                'description' => 'A cozy yet ultra-luxurious 1-bedroom apartment featuring direct views of the Arabian Sea. Equipped with Italian kitchen fittings and smart home controls.',
                'image' => 'assets/images/properties/properties1.png',
                'category_slug' => 'apartment',
                'city_slug' => 'karachi',
                'project_slug' => 'ocean-front-residences',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'swimming_pool' => true,
                'parking' => true,
                'wifi' => true,
                'air_conditioning' => true,
                'security_system' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],
            [
                'name' => 'Ocean Front 2-Bed Luxury Suite',
                'slug' => 'ocean-front-2-bed-luxury-suite',
                'address' => 'Emaar District, Phase 8 DHA, Karachi',
                'price' => 28000000.00,
                'size' => 1450,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'garages' => 1,
                'description' => 'Spacious 2-bedroom residence featuring private balconies overlooking the beach. Perfect for families looking for high-class beachfront living.',
                'image' => 'assets/images/properties/properties2.png',
                'category_slug' => 'apartment',
                'city_slug' => 'karachi',
                'project_slug' => 'ocean-front-residences',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'balcony' => true,
                'wifi' => true,
                'air_conditioning' => true,
                'security_system' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],
            [
                'name' => 'Ocean Front Duplex Penthouse',
                'slug' => 'ocean-front-duplex-penthouse',
                'address' => 'Emaar District, Phase 8 DHA, Karachi',
                'price' => 65000000.00,
                'size' => 3200,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'garages' => 2,
                'description' => 'The crown jewel of Ocean Front. This massive duplex penthouse features double-height ceilings, a private rooftop pool, and 360-degree ocean views.',
                'image' => 'assets/images/properties/properties5.jpg',
                'category_slug' => 'penthouse',
                'city_slug' => 'karachi',
                'project_slug' => 'ocean-front-residences',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'swimming_pool' => true,
                'gym' => true,
                'parking' => true,
                'security_system' => true,
                'wine_cellar' => true,
                'wifi' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Emaar Panorama (Karachi) ───
            [
                'name' => 'Panorama 3-Bed Signature Unit',
                'slug' => 'panorama-3-bed-signature-unit',
                'address' => 'DHA Phase 8, Karachi',
                'price' => 55000000.00,
                'size' => 2100,
                'bedrooms' => 3,
                'bathrooms' => 3,
                'garages' => 2,
                'description' => 'Premium signature 3-bedroom apartment inside Emaar Panorama. Fully finished with premium marble flooring and dynamic glass facade.',
                'image' => 'assets/images/properties/properties3.png',
                'category_slug' => 'apartment',
                'city_slug' => 'karachi',
                'project_slug' => 'emaar-panorama',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'air_conditioning' => true,
                'security_system' => true,
                'gym' => true,
                'parking' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Marina Sports City (Karachi) ───
            [
                'name' => 'Marina Sports City Luxury Villa',
                'slug' => 'marina-sports-city-luxury-villa',
                'address' => 'M-9 Motorway, Karachi',
                'price' => 18000000.00,
                'size' => 2400,
                'bedrooms' => 4,
                'bathrooms' => 4,
                'garages' => 2,
                'description' => 'A double-story luxury villa overlooking the golf course, featuring modern design, lawn, and high-end security.',
                'image' => 'assets/images/properties/properties4.jpg',
                'category_slug' => 'villa',
                'city_slug' => 'karachi',
                'project_slug' => 'marina-sports-city',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'swimming_pool' => true,
                'parking' => true,
                'security_system' => true,
                'wifi' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Palm Jumeirah Villas (Dubai) ───
            [
                'name' => 'Frond Beachfront Signature Villa',
                'slug' => 'frond-beachfront-signature-villa',
                'address' => 'Frond C, Palm Jumeirah, Dubai',
                'price' => 24000000.00,
                'size' => 6500,
                'bedrooms' => 6,
                'bathrooms' => 6,
                'garages' => 3,
                'description' => 'Bespoke ultra-luxury villa offering panoramic views of the Dubai Marina skyline and private beach access. Features a customized infinity pool and elevator.',
                'image' => 'assets/images/properties/properties6.png',
                'category_slug' => 'villa',
                'city_slug' => 'dubai',
                'project_slug' => 'palm-jumeirah-villas',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'swimming_pool' => true,
                'tennis_court' => true,
                'gym' => true,
                'security_system' => true,
                'wifi' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Downtown Heights (Dubai) ───
            [
                'name' => 'Fountain View 1-Bed Apartment',
                'slug' => 'fountain-view-1-bed-apartment',
                'address' => 'Downtown Dubai, Dubai',
                'price' => 1800000.00,
                'size' => 900,
                'bedrooms' => 1,
                'bathrooms' => 1,
                'garages' => 1,
                'description' => 'A chic apartment steps from the Burj Khalifa. Enjoy the iconic Fountain Show directly from your private floor-to-ceiling glass windows.',
                'image' => 'assets/images/properties/properties7.png',
                'category_slug' => 'apartment',
                'city_slug' => 'dubai',
                'project_slug' => 'downtown-heights',
                'type' => 'Residential',
                'is_featured' => false,
                'status' => true,
                'wifi' => true,
                'air_conditioning' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Gulberg Heights (Lahore) ───
            [
                'name' => 'Gulberg Heights Executive Suite',
                'slug' => 'gulberg-heights-executive-suite',
                'address' => 'Gulberg III, Lahore',
                'price' => 24000000.00,
                'size' => 1500,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'garages' => 1,
                'description' => 'Sophisticated apartment in the core business district of Lahore. Close to MM Alam Road, with top shopping, entertainment, and restaurants nearby.',
                'image' => 'assets/images/properties/properties8.jpg',
                'category_slug' => 'apartment',
                'city_slug' => 'lahore',
                'project_slug' => 'gulberg-heights',
                'type' => 'Residential',
                'is_featured' => false,
                'status' => true,
                'parking' => true,
                'laundry_room' => true,
                'dishwasher' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],

            // ─── Centaurus Residences (Islamabad) ───
            [
                'name' => 'Centaurus 2-Bed Luxury Apartment',
                'slug' => 'centaurus-2-bed-luxury-apartment',
                'address' => 'Tower A, Centaurus, F-8, Islamabad',
                'price' => 35000000.00,
                'size' => 1650,
                'bedrooms' => 2,
                'bathrooms' => 2,
                'garages' => 1,
                'description' => 'High-end corporate apartment with breathtaking Margalla Hills view. Centrally located with private lift access and shopping mall access.',
                'image' => 'assets/images/properties/properties9.jpg',
                'category_slug' => 'apartment',
                'city_slug' => 'islamabad',
                'project_slug' => 'centaurus-residences',
                'type' => 'Residential',
                'is_featured' => true,
                'status' => true,
                'air_conditioning' => true,
                'security_system' => true,
                'wifi' => true,
                'dishwasher' => true,
                'microwave' => true,
                'gallery_images' => $gallery_images,
                'floor_plan_images' => $floor_plan_images,
            ],
        ];

        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('properties');
        foreach ($properties as $propertyData) {
            $categorySlug = $propertyData['category_slug'] ?? null;
            $citySlug = $propertyData['city_slug'] ?? null;
            $projectSlug = $propertyData['project_slug'] ?? null;

            unset($propertyData['category_slug'], $propertyData['city_slug'], $propertyData['project_slug'], $propertyData['type']);

            $propertyData['property_type_id'] = $categorySlug ? ($propertyTypes[$categorySlug] ?? null) : null;
            $propertyData['city_id'] = $citySlug ? ($cities[$citySlug] ?? null) : null;
            $propertyData['project_id'] = $projectSlug ? ($projects[$projectSlug] ?? null) : null;

            // Only keep keys that exist as columns in the properties table
            $propertyData = array_intersect_key($propertyData, array_flip($columns));

            Property::updateOrCreate(
                ['slug' => $propertyData['slug']],
                $propertyData
            );
        }
    }
}
