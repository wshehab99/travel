<?php

namespace Tests\Feature;

use App\Models\Travel;
use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourListTest extends TestCase
{
    use RefreshDatabase;
    public function test_tours_list_by_travel_slug_returned_correct_tours(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/'.$travel->slug.'/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id'=>$tour->id]);
    }
    public function test_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 123.45,
        ]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['price'=>'123.45']);
    }
    public function test_tour_list_returns_pagination(): void
    {
        $toursPerPage = config('app.pagination.tours');
        $travel = Travel::factory()->create();
        $tour = Tour::factory($toursPerPage + 1)->create(['travel_id' => $travel->id]);
        $response = $this->get('/api/v1/travels/' . $travel->slug . '/tours');
        $response->assertStatus(200);
        $response->assertJsonCount($toursPerPage,'data');
        $response->assertJsonPath('meta.current_page',1);
    }
    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $later_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $earlier_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'starting_date' => now()->addDays(1),
            'ending_date' => now()->addDays(3),
        ]);
        $response = $this->get($this->tourUrl($travel->slug));
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',$earlier_tour->id);
        $response->assertJsonPath('data.1.id',$later_tour->id);
    }
    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensive_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 500,
        ]);
        $cheaper_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $cheaper_earlier_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(3),
        ]);
        $response = $this->get($this->tourUrl($travel->slug, '?sort_by=price&sort_order=asc'));
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id',$cheaper_earlier_tour->id);
        $response->assertJsonPath('data.1.id',$cheaper_tour->id);
        $response->assertJsonPath('data.2.id',$expensive_tour->id);
    }
    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensive_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);
        $cheaper_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);
    
        $response = $this->get($this->tourUrl($travel->slug, '?price_from=100'));
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id' => $cheaper_tour->id]);
        $response->assertJsonFragment(['id' => $expensive_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?price_from=150'));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id' => $cheaper_tour->id]);
        $response->assertJsonFragment(['id' => $expensive_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?price_from=250'));
        $response->assertStatus(200);
        $response->assertJsonCount(0,'data');

        $response = $this->get($this->tourUrl($travel->slug, '?price_to=200'));
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id' => $cheaper_tour->id]);
        $response->assertJsonFragment(['id' => $expensive_tour->id]);


        $response = $this->get($this->tourUrl($travel->slug, '?price_to=150'));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id' => $cheaper_tour->id]);
        $response->assertJsonMissing(['id' => $expensive_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?price_to=50'));
        $response->assertStatus(200);
        $response->assertJsonCount(0,'data');

        $response = $this->get($this->tourUrl($travel->slug, '?price_from=150&price_to=250'));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id' => $cheaper_tour->id]);
        $response->assertJsonFragment(['id' => $expensive_tour->id]);

    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $later_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(3),
        ]);
        $earlier_tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'starting_date' => now(),
            'ending_date' => now()->addDays(3),
        ]);
    
        $response = $this->get($this->tourUrl($travel->slug, '?date_from='.now()));
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id' => $later_tour->id]);
        $response->assertJsonFragment(['id' => $earlier_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?date_from='.now()->addDay()));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id' => $later_tour->id]);
        $response->assertJsonMissing(['id' => $earlier_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?date_from='.now()->addDays(5)));
        $response->assertStatus(200);
        $response->assertJsonCount(0,'data');

        $response = $this->get($this->tourUrl($travel->slug, '?date_to='.now()->addDays(5)));
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['id' => $later_tour->id]);
        $response->assertJsonFragment(['id' => $earlier_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?date_to='.now()->addDay()));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonMissing(['id' => $later_tour->id]);
        $response->assertJsonFragment(['id' => $earlier_tour->id]);

        $response = $this->get($this->tourUrl($travel->slug, '?date_to='.now()->subDay()));
        $response->assertStatus(200);
        $response->assertJsonCount(0,'data');



        $response = $this->get($this->tourUrl($travel->slug, '?date_from='.now()->addDay() . '&date_to=' . now()->addDays(5)));
        $response->assertStatus(200);
        $response->assertJsonCount(1,'data');
        $response->assertJsonFragment(['id' => $later_tour->id]);
        $response->assertJsonMissing(['id' => $earlier_tour->id]);
    }
    public function test_tour_list_returns_validation_errors() : void 
    {
        $travel = Travel::factory()->create();
        $response = $this->getJson($this->tourUrl($travel->slug, '?date_from=abcde'));
        $response->assertStatus(422);

        $response = $this->getJson($this->tourUrl($travel->slug, '?price_from=abcde'));
        $response->assertStatus(422);

        $response = $this->getJson($this->tourUrl($travel->slug, '?sort_by=abcde&sort_order=asc'));
        $response->assertStatus(422);

         $response = $this->getJson($this->tourUrl($travel->slug, '?sort_by=price&sort_order=abcde'));
        $response->assertStatus(422);

    }
    protected function tourUrl($slug, string $parms = '') : string 
    {
        return '/api/v1/travels/' . $slug . '/tours' . $parms;
    }
}
