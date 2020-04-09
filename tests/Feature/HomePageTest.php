<?php

namespace Tests\Feature;

use App\Ssml;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_it_loads_the_form()
    {
        $ssmls = factory(Ssml::class, 20)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
        $ssmls->each(function ($ssml) use ($response) {
            $response->assertSee($ssml->id);
            $response->assertSee($ssml->title);
            $response->assertSee($ssml->link);
        });
    }
}
