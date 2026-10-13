<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            [
                'name' => 'Parking',
                'slug' => 'parking',
                'description' => 'Dedicated covered or open car parking space',
                'icon' => 'parking',
            ],
            [
                'name' => 'Lift',
                'slug' => 'lift',
                'description' => 'High-speed passenger and service elevators',
                'icon' => 'elevator',
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => '24/7 manned security guards and visitor logs',
                'icon' => 'shield-check',
            ],
            [
                'name' => 'CCTV',
                'slug' => 'cctv',
                'description' => 'Comprehensive perimeter and lobby CCTV surveillance',
                'icon' => 'camera',
            ],
            [
                'name' => 'Gym',
                'slug' => 'gym',
                'description' => 'Fully-equipped modern fitness center',
                'icon' => 'dumbbell',
            ],
            [
                'name' => 'Swimming Pool',
                'slug' => 'swimming-pool',
                'description' => 'Full-size adult pool with dedicated kids pool area',
                'icon' => 'water',
            ],
            [
                'name' => 'Club House',
                'slug' => 'club-house',
                'description' => 'Multi-purpose community hall and lounge',
                'icon' => 'home',
            ],
            [
                'name' => 'Garden',
                'slug' => 'garden',
                'description' => 'Landscaped green parks and walking tracks',
                'icon' => 'tree',
            ],
            [
                'name' => 'Power Backup',
                'slug' => 'power-backup',
                'description' => '100% DG power backup for common areas and apartments',
                'icon' => 'bolt',
            ],
            [
                'name' => 'Children\'s Play Area',
                'slug' => 'children-play-area',
                'description' => 'Secure outdoor playground with modern rides',
                'icon' => 'sparkles',
            ],
            [
                'name' => 'Sports Facility',
                'slug' => 'sports-facility',
                'description' => 'Badminton, tennis courts, and indoor games room',
                'icon' => 'trophy',
            ],
            [
                'name' => 'Visitor Parking',
                'slug' => 'visitor-parking',
                'description' => 'Dedicated bays for guest and visitor vehicles',
                'icon' => 'car',
            ],
            [
                'name' => 'Air Conditioning',
                'slug' => 'air-conditioning',
                'description' => 'Central or split AC provisions installed',
                'icon' => 'snowflake',
            ],
            [
                'name' => 'Internet',
                'slug' => 'internet',
                'description' => 'Pre-installed high-speed FTTH fiber broadband connectivity',
                'icon' => 'wifi',
            ],
            [
                'name' => 'Gas Pipeline',
                'slug' => 'gas-pipeline',
                'description' => 'Reticulated piped natural gas connection directly to kitchen',
                'icon' => 'fire',
            ],
        ];

        foreach ($amenities as $amenity) {
            Amenity::updateOrCreate(
                ['slug' => $amenity['slug']],
                $amenity
            );
        }
    }
}
