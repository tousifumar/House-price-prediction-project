<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Locality;
use App\Models\MlModel;
use App\Models\Prediction;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeds_correctly(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', ['email' => 'admin@houseprice.ai', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'demo@houseprice.ai', 'role' => 'user']);
        $this->assertDatabaseHas('cities', ['slug' => 'bangalore']);
        $this->assertDatabaseCount('localities', 10);
        $this->assertDatabaseCount('amenities', 15);
        $this->assertDatabaseHas('ml_models', ['version' => 'v1.0-xgb', 'status' => 'active']);
        $this->assertGreaterThan(0, Property::count());
        $this->assertGreaterThan(0, Prediction::count());
    }
}
