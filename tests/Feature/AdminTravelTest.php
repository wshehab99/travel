<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_travel(): void
    {
        $response = $this->postJson('/api/v1/admin/travels',[

        ]);

        $response->assertStatus(401);
    }
    public function test_non_admin_user_cannot_access_adding_travel(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name','user')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels',[

        ]);

        $response->assertStatus(403);
    }
    public function test_save_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels',[
            'name' =>'admin',
        ]);

        $response->assertStatus(422);

        
        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels',[
            'name' =>'travel admin',
            'is_public' => 1,
            'description' => 'admin app',
            'number_of_days' => 5,
        ]);
        $response->assertStatus(201);
    }
}
