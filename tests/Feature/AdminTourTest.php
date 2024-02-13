<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;
    public function test_public_user_cannot_access_adding_tour(): void
    {
        $travel = Travel::factory()->create();
        $response = $this->postJson($this->url($travel->slug),[]);
        $response->assertStatus(401);
    }
    public function test_non_admin_user_cannot_access_adding_tour(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name','user')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson($this->url($travel->slug),[]);
        $response->assertStatus(403);
    }
    public function test_save_tour_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name','admin')->value('id'));
        $travel = Travel::factory()->create();
        $response = $this->actingAs($user)->postJson($this->url($travel->slug),['name' =>'admin tour']);
        $response->assertStatus(422);

        
        $response = $this->actingAs($user)->postJson($this->url($travel->slug),[
            'name' =>'admin tour',
            'starting_date' => now()->toDateString(),
            'ending_date' => now()->addDay()->toDateString(),
            'price' => 123.45,
        ]);

        $response->assertStatus(201);

        
        $response = $this->get($this->getTourUrl($travel->slug));
        $response->assertJsonFragment(['name' =>'admin tour']);
    }
    protected function url(string $slug): string
    {
        return '/api/v1/admin/travels/' . $slug .'/tours';
    }
    protected function getTourUrl(string $slug): string
    {
        return '/api/v1/travels/' . $slug .'/tours';
    }
}
