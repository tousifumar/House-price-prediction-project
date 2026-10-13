<?php

namespace Tests\Feature;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Dataset;
use App\Models\Locality;
use App\Models\MlModel;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@houseprice.ai')->first();
        $this->regularUser = User::where('email', 'demo@houseprice.ai')->first();
    }

    public function test_guests_and_regular_users_cannot_access_admin_dashboard(): void
    {
        // Guest redirected to login
        $guestRes = $this->get('/admin');
        $guestRes->assertRedirect('/login');

        // Regular user redirected to login
        $userRes = $this->actingAs($this->regularUser)->get('/admin');
        $userRes->assertRedirect('/login');
        $userRes->assertSessionHas('error');
    }

    public function test_admin_can_access_all_admin_pages(): void
    {
        $pages = [
            '/admin',
            '/admin/users',
            '/admin/properties',
            '/admin/locations',
            '/admin/amenities',
            '/admin/datasets',
            '/admin/models',
        ];

        foreach ($pages as $page) {
            $response = $this->actingAs($this->admin)->get($page);
            $response->assertStatus(200);
        }
    }

    public function test_admin_can_manage_user_roles_and_status(): void
    {
        // Promote regular user to admin
        $res1 = $this->actingAs($this->admin)->patch(route('admin.users.role', $this->regularUser->id), [
            'role' => 'admin',
        ]);
        $res1->assertSessionHas('success');
        $this->assertEquals('admin', $this->regularUser->fresh()->role);

        // Suspend user
        $res2 = $this->actingAs($this->admin)->patch(route('admin.users.status', $this->regularUser->id));
        $res2->assertSessionHas('success');
        $this->assertEquals('blocked', $this->regularUser->fresh()->status);

        // Prevent self-demotion
        $res3 = $this->actingAs($this->admin)->patch(route('admin.users.role', $this->admin->id), [
            'role' => 'user',
        ]);
        $res3->assertSessionHas('error');
        $this->assertEquals('admin', $this->admin->fresh()->role);
    }

    public function test_admin_can_crud_properties(): void
    {
        $locality = Locality::first();
        $amenity = Amenity::first();

        // Create property
        $createRes = $this->actingAs($this->admin)->post(route('admin.properties.store'), [
            'title' => 'Sobha Dream Acres 3BHK',
            'locality_id' => $locality->id,
            'property_type' => 'Apartment',
            'transaction_type' => 'Sale',
            'price' => 12500000,
            'area' => 1450,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'floor' => 4,
            'total_floors' => 12,
            'property_age' => 2,
            'furnishing' => 'Semi-Furnished',
            'amenities' => [$amenity->id],
        ]);
        $createRes->assertSessionHas('success');

        $property = Property::where('title', 'Sobha Dream Acres 3BHK')->first();
        $this->assertNotNull($property);
        $this->assertTrue($property->amenities->contains($amenity));

        // Delete property
        $delRes = $this->actingAs($this->admin)->delete(route('admin.properties.destroy', $property->id));
        $delRes->assertSessionHas('success');
        $this->assertSoftDeleted('properties', ['id' => $property->id]);
    }

    public function test_admin_can_manage_localities_and_benchmarks(): void
    {
        $city = City::first();

        // Register locality
        $regRes = $this->actingAs($this->admin)->post(route('admin.locations.localities.store'), [
            'city_id' => $city->id,
            'name' => 'Kanakapura Road Extension',
            'avg_price_per_sqft' => 7800,
            'growth_rate' => 9.2,
            'location_score' => 8.1,
            'price_tier' => 'Mid',
        ]);
        $regRes->assertSessionHas('success');

        $locality = Locality::where('name', 'Kanakapura Road Extension')->first();
        $this->assertNotNull($locality);

        // Update benchmarks
        $updateRes = $this->actingAs($this->admin)->put(route('admin.locations.localities.update', $locality->id), [
            'avg_price_per_sqft' => 8200,
            'growth_rate' => 10.1,
            'location_score' => 8.5,
            'price_tier' => 'High',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals(8200, $locality->fresh()->avg_price_per_sqft);

        // Toggle active status
        $toggleRes = $this->actingAs($this->admin)->patch(route('admin.locations.localities.toggle', $locality->id));
        $toggleRes->assertSessionHas('success');
        $this->assertFalse((bool) $locality->fresh()->is_active);
    }

    public function test_admin_can_upload_and_preview_dataset(): void
    {
        $csvContent = "area,bedrooms,bathrooms,floor,total_floors,property_age,locality,property_type,price\n" .
                      "1200,2,2,3,10,2,Whitefield,Apartment,9500000\n" .
                      "1600,3,2,6,12,1,Indiranagar,Apartment,18500000\n";

        $file = UploadedFile::fake()->createWithContent('test_sales.csv', $csvContent);

        $uploadRes = $this->actingAs($this->admin)->post(route('admin.datasets.store'), [
            'name' => 'Q1 Sample Sales',
            'dataset_file' => $file,
            'description' => 'Test dataset for unit tests',
        ]);
        $uploadRes->assertSessionHas('success');

        $dataset = Dataset::where('name', 'Q1 Sample Sales')->first();
        $this->assertNotNull($dataset);
        $this->assertEquals(2, $dataset->row_count);

        // Preview endpoint
        $previewRes = $this->actingAs($this->admin)->getJson(route('admin.datasets.preview', $dataset->id));
        $previewRes->assertStatus(200);
        $previewRes->assertJsonStructure(['name', 'headers', 'rows', 'row_count', 'column_count']);
        $this->assertCount(2, $previewRes->json('rows'));
    }

    public function test_admin_can_activate_ml_model(): void
    {
        $model = MlModel::where('status', 'inactive')->first();

        if (!$model) {
            $model = MlModel::create([
                'name' => 'Candidate Model',
                'version' => 'v9.9.0-test',
                'algorithm' => 'XGBoost',
                'model_path' => 'ml-service/models/test.joblib',
                'preprocessor_path' => 'ml-service/models/test_prep.joblib',
                'r2_score' => 0.985,
                'mae' => 750000,
                'rmse' => 1100000,
                'status' => 'inactive',
            ]);
        }

        $activateRes = $this->actingAs($this->admin)->patch(route('admin.models.activate', $model->id));
        $activateRes->assertSessionHas('success');

        $this->assertEquals('active', $model->fresh()->status);
    }
}
