<?php

namespace Tests\Unit;

use App\Models\Record;
use Tests\TestCase;

class RecordTest extends TestCase
{
    /** @test */
    public function it_format_prices()
    {
        $record = Record::factory()->make([
            'original' => 111111.11,
            'sale'     => 222222,
        ]);

        $this->assertEquals('111,111.11', $record->original);
        $this->assertEquals('222,222.00', $record->sale);
    }
}
