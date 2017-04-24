<?php

namespace Tests\Unit;

use App\Record;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RecordTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_format_prices()
    {
        $record = factory(Record::class)->create([
            'original' => 111111,
            'sale' => 222222
        ]);

        $this->assertEquals('1,111.11', $record->original);
        $this->assertEquals('2,222.22', $record->sale);
    }
}
