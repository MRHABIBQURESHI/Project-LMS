<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\City;
use App\Models\Project;
use App\Models\Developer;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = City::all();
        $projects = Project::all();
        $developers = Developer::all();
        $propertyTypes = PropertyType::all();

        $teams = [
            [
                'name' => 'Amelia Margaret',
                'slug' => 'amelia-margaret',
                'designation' => 'Real Estate Broker',
                'description' => 'Huge number properties available here for buy, sell and Rent, you can find here co-living property lots to choose.',
                'image' => 'assets/images/team/person1.png',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'is_featured' => true,
                'status' => true,
                'city_id' => $cities->where('slug', 'karachi')->first()?->id ?? $cities->first()?->id,
                'project_id' => $projects->where('slug', 'ocean-front-residences')->first()?->id ?? $projects->first()?->id,
                'developer_id' => $developers->first()?->id,
                'property_type_id' => $propertyTypes->first()?->id,
            ],
            [
                'name' => 'Stephen Kelvin',
                'slug' => 'stephen-kelvin',
                'designation' => 'Real Estate Agent',
                'description' => 'Huge number properties available here for buy, sell and Rent, you can find here co-living property lots to choose.',
                'image' => 'assets/images/team/person2.png',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'is_featured' => true,
                'status' => true,
                'city_id' => $cities->where('slug', 'lahore')->first()?->id ?? $cities->first()?->id,
                'project_id' => $projects->where('slug', 'gulberg-heights')->first()?->id ?? $projects->first()?->id,
                'developer_id' => $developers->first()?->id,
                'property_type_id' => $propertyTypes->first()?->id,
            ],
            [
                'name' => 'Michael Richard',
                'slug' => 'michael-richard',
                'designation' => 'Real Estate Broker',
                'description' => 'Huge number properties available here for buy, sell and Rent, you can find here co-living property lots to choose.',
                'image' => 'assets/images/team/person3.png',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'is_featured' => true,
                'status' => true,
                'city_id' => $cities->where('slug', 'islamabad')->first()?->id ?? $cities->first()?->id,
                'project_id' => $projects->where('slug', 'centaurus-residences')->first()?->id ?? $projects->first()?->id,
                'developer_id' => $developers->first()?->id,
                'property_type_id' => $propertyTypes->first()?->id,
            ],
            [
                'name' => 'Sarah Jenkins',
                'slug' => 'sarah-jenkins',
                'designation' => 'Property Consultant',
                'description' => 'Huge number properties available here for buy, sell and Rent, you can find here co-living property lots to choose.',
                'image' => 'assets/images/team/person4.png',
                'facebook' => 'https://facebook.com',
                'twitter' => 'https://twitter.com',
                'instagram' => 'https://instagram.com',
                'is_featured' => true,
                'status' => true,
                'city_id' => $cities->where('slug', 'dubai')->first()?->id ?? $cities->first()?->id,
                'project_id' => $projects->where('slug', 'palm-jumeirah-villas')->first()?->id ?? $projects->first()?->id,
                'developer_id' => $developers->first()?->id,
                'property_type_id' => $propertyTypes->first()?->id,
            ],
        ];

        foreach ($teams as $teamData) {
            Agent::updateOrCreate(
                ['slug' => $teamData['slug']],
                $teamData
            );
        }
    }
}
