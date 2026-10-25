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
            ->assertSee('$233,448.27')
            ->assertSee('$229,259.34')
            ->assertSee('2%</strong> off', false);
    }

    #[Test]
    public function it_shows_facts_about_past_sales()
    {
        Record::factory()->create(['original' => 100000, 'sale' => 99000, 'created_at' => '2024-06-20']);
        Record::factory()->create(['original' => 100000, 'sale' => 59000, 'created_at' => '2024-06-27']);
        Record::factory()->create(['original' => 100000, 'sale' => 62000, 'created_at' => '2024-06-28']);
        Record::factory()->create(['original' => 110000, 'sale' => 81400, 'created_at' => '2024-11-27']);
        Record::factory()->create(['original' => 120000, 'sale' => 118000, 'created_at' => '2024-12-05']);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('1.2×')
            ->assertSee('Deepest discount on record, during Summer Sale 2024.')
            ->assertSee('Lowest total during Autumn Sale 2024, 26% below full price.')
            ->assertSee('$814');
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
    public function api_returns_the_cheapest_record_of_each_week()
    {
        // 2024-01-01 is a Monday.
        Record::factory()->create(['original' => 100, 'sale' => 90, 'created_at' => '2024-01-01 10:00:00']);
        Record::factory()->create(['original' => 100, 'sale' => 60, 'created_at' => '2024-01-03 10:00:00']);
        Record::factory()->create(['original' => 100, 'sale' => 95, 'created_at' => '2024-01-07 10:00:00']);
        Record::factory()->create(['original' => 200, 'sale' => 180, 'created_at' => '2024-01-08 10:00:00']);

        $this->get('/api/records?interval=week')
            ->assertSuccessful()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.sale', 0.6)
            ->assertJsonPath('data.0.created_at', '2024-01-03T10:00:00+00:00')
            ->assertJsonPath('data.1.sale', 1.8);

        $this->get('/api/records')->assertJsonCount(4, 'data');
    }

    #[Test]
    public function api_cache_picks_up_new_records()
    {
        Record::factory()->create();
        $this->get('/api/records')->assertJsonCount(1, 'data');

        Record::factory()->create();
        $this->get('/api/records')->assertJsonCount(2, 'data');
    }

    #[Test]
    public function it_display_message_when_no_data_available()
    {
        $this->get('/')
            ->assertSuccessful()
            ->assertSee('No data available.');
    }

    #[Test]
    public function it_ignores_invalid_locale()
    {
        $this->get('/?locale=../../etc/passwd')
            ->assertSuccessful()
            ->assertSee('<html lang="en">', false);

        $this->get('/?locale[]=zh')->assertSuccessful();
    }
}
