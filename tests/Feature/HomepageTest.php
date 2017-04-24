<?php

namespace Tests\Feature;

use App\Record;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_works()
    {
        $this->get('/')->assertStatus(200);
    }

    /** @test */
    public function api_works()
    {
        $record = factory(Record::class)->create();

        $this->get('/api')
            ->assertJsonFragment([
                'original' => $record->original
            ]);
    }
}
