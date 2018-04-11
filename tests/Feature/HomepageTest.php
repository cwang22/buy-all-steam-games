<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_works()
    {
        $this->get('/')->assertSuccessful();
    }

    /** @test */
    public function api_works()
    {
        $this->get('/api')->assertSuccessful();
    }

    /** @test */
    public function it_display_message_when_no_data_available()
    {
        $this->get('/')->assertSee('No data available.');
    }
}
