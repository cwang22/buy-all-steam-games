<?php

namespace Tests\Unit;

use App\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_format_prices()
    {
        $record = factory(Record::class)->create([
            'original' => 111111.11,
            'sale' => 222222,
        ]);

        $this->assertEquals('111,111.11', $record->original);
        $this->assertEquals('222,222.00', $record->sale);
    }
}
