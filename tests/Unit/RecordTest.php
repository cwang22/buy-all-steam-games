<?php

namespace Tests\Unit;

use App\Http\Resources\RecordResource;
use App\Models\Record;
use Illuminate\Http\Request;
use Tests\TestCase;

class RecordTest extends TestCase
{
    /** @test */
    public function it_converts_cents_to_dollars()
    {
        $record = Record::factory()->make([
            'original' => 11111111,
            'sale'     => 22222200,
            'created_at' => now(),
        ]);

        $data = (new RecordResource($record))->toArray(Request::create('/'));

        $this->assertSame(111111.11, $data['original']);
        $this->assertEquals(222222, $data['sale']);
    }
}
