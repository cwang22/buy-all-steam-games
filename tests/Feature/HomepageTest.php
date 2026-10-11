<?php

namespace Tests\Feature;

use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_works()
    {
        Record::factory()->create(['original' => 23344827, 'sale' => 22925934]);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('$233448.27')
            ->assertSee('$229259.34');
    }

    #[Test]
    public function api_works()
    {
        Record::factory()->create(['original' => 23344827, 'sale' => 22925934]);

        $this->get('/api/records')
            ->assertSuccessful()
            ->assertJsonPath('data.0.original', 233448.27)
            ->assertJsonPath('data.0.sale', 229259.34);
    }

    #[Test]
    public function it_display_message_when_no_data_available()
    {
        $this->get('/')
            ->assertSuccessful()
            ->assertSee('No data available.');
    }
}
