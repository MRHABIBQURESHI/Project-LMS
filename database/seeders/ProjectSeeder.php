<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Project;
use App\Models\Developer;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::pluck('id', 'slug');

        $projects = [
            // ─── KARACHI (10 Projects) ───
            [
                'name' => 'Marina Sports City',
                'slug' => 'marina-sports-city',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties1.png',
                'description' => 'A master-planned sports-themed community in Karachi offering state-of-the-art sporting facilities, premium villas, and modern apartments.',
                'developer' => 'Al-Kabir Developers',
                'location' => 'M-9 Motorway, Karachi',
                'completion_year' => 2026,
                'starting_price' => 3500000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Ocean Front Residences',
                'slug' => 'ocean-front-residences',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties2.png',
                'description' => 'Premium luxury apartments situated right on the Arabian Sea front, featuring unmatched panoramic views, private beach access, and world-class amenities.',
                'developer' => 'Coastal Builders & Developers',
                'location' => 'Emaar District, Phase 8 DHA, Karachi',
                'completion_year' => 2027,
                'starting_price' => 18000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Emaar Panorama',
                'slug' => 'emaar-panorama',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties3.png',
                'description' => 'Experience the epitome of waterfront living at Panorama, featuring premium signature apartments and penthouses overlooking the Arabian Sea.',
                'developer' => 'Emaar Pakistan',
                'location' => 'DHA Phase 8, Karachi',
                'completion_year' => 2025,
                'starting_price' => 45000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Crescent Bay Towers',
                'slug' => 'crescent-bay-towers',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties4.jpg',
                'description' => 'An iconic vibrant community of high-rise towers offering luxury apartments and a unique lifestyle along the shore of Karachi.',
                'developer' => 'Emaar Pakistan',
                'location' => 'DHA Phase 8, Karachi',
                'completion_year' => 2025,
                'starting_price' => 32000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Bahria Town Heights',
                'slug' => 'bahria-town-heights',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties5.jpg',
                'description' => 'Modern lifestyle apartments with top-of-the-line amenities, backup power generation, and commercial hubs at their doorstep in Bahria Town.',
                'developer' => 'Bahria Town',
                'location' => 'Bahria Town, Karachi',
                'completion_year' => 2024,
                'starting_price' => 7500000.00,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'name' => 'HMR Waterfront',
                'slug' => 'hmr-waterfront',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties6.png',
                'description' => 'A secure gated community of luxury high-rise residential towers complemented by premium retail, dining, and recreational spaces on the coast.',
                'developer' => 'HMR Group',
                'location' => 'DHA Phase 8, Karachi',
                'completion_year' => 2026,
                'starting_price' => 28000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'DHA Oasis Villas',
                'slug' => 'dha-oasis-villas',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties7.png',
                'description' => 'Exquisite premium ready-to-move villas featuring signature architecture, community centers, parks, and top-tier security within DHA City.',
                'developer' => 'DHA Karachi',
                'location' => 'DHA City, Karachi',
                'completion_year' => 2024,
                'starting_price' => 35000000.00,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'name' => 'Clifton Vista Apartments',
                'slug' => 'clifton-vista-apartments',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties8.jpg',
                'description' => 'Sophisticated residential units located in the heart of Clifton, offering beautiful sea breeze, security, and close proximity to premium shopping malls.',
                'developer' => 'Vista Developers',
                'location' => 'Clifton Block 4, Karachi',
                'completion_year' => 2025,
                'starting_price' => 15000000.00,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'name' => 'Karsaz Executive Suites',
                'slug' => 'karsaz-executive-suites',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties9.jpg',
                'description' => 'High-end apartments designed for corporate executives, located centrally in Karsaz with top connectivity to airport and business hubs.',
                'developer' => 'Elite Builders',
                'location' => 'Karsaz, Karachi',
                'completion_year' => 2025,
                'starting_price' => 12000000.00,
                'is_featured' => false,
                'status' => true,
            ],
            [
                'name' => 'Malir Hills Country Club',
                'slug' => 'malir-hills-country-club',
                'city_id' => $cities['karachi'] ?? null,
                'image' => 'assets/images/properties/properties1.png',
                'description' => 'Escape the city rush in this serene country club setting in Malir, featuring resort-style residential chalets and top-class leisure amenities.',
                'developer' => 'Greenfield Developers',
                'location' => 'Malir Cantonment, Karachi',
                'completion_year' => 2026,
                'starting_price' => 5500000.00,
                'is_featured' => false,
                'status' => true,
            ],

            // ─── DUBAI (3 Projects) ───
            [
                'name' => 'Palm Jumeirah Villas',
                'slug' => 'palm-jumeirah-villas',
                'city_id' => $cities['dubai'] ?? null,
                'image' => 'assets/images/properties/properties4.jpg',
                'description' => 'Ultra-luxury bespoke beachside villas on the world-famous Palm Jumeirah fronds, featuring private beaches, infinity pools, and dynamic modern architecture.',
                'developer' => 'Nakheel',
                'location' => 'Palm Jumeirah, Dubai',
                'completion_year' => 2025,
                'starting_price' => 12000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Downtown Heights',
                'slug' => 'downtown-heights',
                'city_id' => $cities['dubai'] ?? null,
                'image' => 'assets/images/properties/properties2.png',
                'description' => 'Sleek luxury residential high-rise situated steps away from the Burj Khalifa and Dubai Mall, offering stunning views of the Dubai Fountain.',
                'developer' => 'Emaar Properties',
                'location' => 'Downtown Dubai, Dubai',
                'completion_year' => 2026,
                'starting_price' => 1800000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Dubai Marina Sands',
                'slug' => 'dubai-marina-sands',
                'city_id' => $cities['dubai'] ?? null,
                'image' => 'assets/images/properties/properties6.png',
                'description' => 'Spectacular waterfront apartments in Dubai Marina with stunning views of yachts, high-end restaurants, and quick beach access.',
                'developer' => 'Select Group',
                'location' => 'Dubai Marina, Dubai',
                'completion_year' => 2024,
                'starting_price' => 2500000.00,
                'is_featured' => true,
                'status' => true,
            ],

            // ─── LAHORE (3 Projects) ───
            [
                'name' => 'Gulberg Heights',
                'slug' => 'gulberg-heights',
                'city_id' => $cities['lahore'] ?? null,
                'image' => 'assets/images/properties/properties3.png',
                'description' => 'Luxury apartments and commercial retail outlets situated in Gulberg, the financial hub of Lahore, with premium views and security.',
                'developer' => 'Lahore Builders',
                'location' => 'Gulberg III, Lahore',
                'completion_year' => 2025,
                'starting_price' => 12000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'DHA Phase 6 Villas',
                'slug' => 'dha-phase-6-villas',
                'city_id' => $cities['lahore'] ?? null,
                'image' => 'assets/images/properties/properties7.png',
                'description' => 'Modern 1-kanal and 10-marla residential villas in Phase 6 DHA Lahore, representing premium luxury with green community gardens.',
                'developer' => 'DHA Lahore',
                'location' => 'DHA Phase 6, Lahore',
                'completion_year' => 2024,
                'starting_price' => 48000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Bahria Meadows',
                'slug' => 'bahria-meadows',
                'city_id' => $cities['lahore'] ?? null,
                'image' => 'assets/images/properties/properties8.jpg',
                'description' => 'Resort-style residential community with private gardens, lakes, and safari theme parks in Bahria Town Lahore.',
                'developer' => 'Bahria Town',
                'location' => 'Bahria Town, Lahore',
                'completion_year' => 2024,
                'starting_price' => 16000000.00,
                'is_featured' => false,
                'status' => true,
            ],

            // ─── ISLAMABAD (3 Projects) ───
            [
                'name' => 'Centaurus Residences',
                'slug' => 'centaurus-residences',
                'city_id' => $cities['islamabad'] ?? null,
                'image' => 'assets/images/properties/properties5.jpg',
                'description' => 'Ultra-luxury living in the iconic Centaurus towers of Islamabad, offering premium services, swimming pools, and access to the mega mall.',
                'developer' => 'PGCL',
                'location' => 'F-8, Islamabad',
                'completion_year' => 2023,
                'starting_price' => 25000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Eighteen Luxury Villas',
                'slug' => 'eighteen-luxury-villas',
                'city_id' => $cities['islamabad'] ?? null,
                'image' => 'assets/images/properties/properties1.png',
                'description' => 'World-class premium gated resort-style community featuring an 18-hole championship golf course and breathtaking scenic views of Margalla.',
                'developer' => 'Ora Developers',
                'location' => 'Kashmir Highway, Islamabad',
                'completion_year' => 2026,
                'starting_price' => 65000000.00,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Gulberg Greens Enclave',
                'slug' => 'gulberg-greens-enclave',
                'city_id' => $cities['islamabad'] ?? null,
                'image' => 'assets/images/properties/properties9.jpg',
                'description' => 'Luxury farmhouses and modern high-end apartments surrounded by beautiful landscaping and green gardens in Gulberg Greens.',
                'developer' => 'IBECHS',
                'location' => 'Gulberg Greens, Islamabad',
                'completion_year' => 2025,
                'starting_price' => 14000000.00,
                'is_featured' => false,
                'status' => true,
            ],
        ];

        foreach ($projects as $projectData) {
            $developerName = $projectData['developer'] ?? null;
            if ($developerName) {
                $developer = Developer::firstOrCreate(
                    ['name' => $developerName],
                    [
                        'status' => true,
                        'logo' => 'assets/images/brand/brand' . rand(1, 5) . '.png',
                        'website_url' => '#'
                    ]
                );
                $projectData['developer_id'] = $developer->id;
            }
            unset($projectData['developer']);

            Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                $projectData
            );
        }
    }
}
