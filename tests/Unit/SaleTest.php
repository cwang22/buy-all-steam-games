<?php

namespace Tests\Unit;

use App\Models\Record;
use App\Support\Sale;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SaleTest extends TestCase
{
    /**
     * @param  array<string, int>  $sales  date => percent off a 1000 full price
     * @return Record[]
     */
    private function records(array $sales): array
    {
        return collect($sales)->map(fn (int $percentOff, string $date) => Record::factory()->make([
            'original' => 1000,
            'sale' => 1000 - $percentOff * 10,
            'created_at' => $date,
        ]))->values()->all();
    }

    #[Test]
    public function it_groups_consecutive_discounted_days_into_one_sale()
    {
        $sales = Sale::detect($this->records([
            '2025-06-24' => 3,
            '2025-06-26' => 30,
            '2025-06-27' => 35,
            '2025-06-28' => 33,
            '2025-07-09' => 4,
            '2025-10-30' => 25,
            '2025-10-31' => 2,
        ]));

        $this->assertCount(2, $sales);

        $this->assertSame('2025-06-26', $sales[0]->start->toDateString());
        $this->assertSame('2025-06-28', $sales[0]->end->toDateString());
        $this->assertSame(650, $sales[0]->lowestSale);
        $this->assertEqualsWithDelta(0.35, $sales[0]->deepestDiscount, 0.0001);
        $this->assertFalse($sales[0]->ongoing);
        $this->assertSame('Summer Sale 2025', $sales[0]->name());

        // A one-day sale still counts.
        $this->assertSame('2025-10-30', $sales[1]->start->toDateString());
        $this->assertSame('the sale of October 2025', $sales[1]->name());
    }

    #[Test]
    public function it_marks_a_sale_running_at_the_latest_record_as_ongoing()
    {
        $sales = Sale::detect($this->records([
            '2025-12-18' => 2,
            '2025-12-19' => 38,
            '2025-12-20' => 40,
        ]));

        $this->assertCount(1, $sales);
        $this->assertTrue($sales[0]->ongoing);
        $this->assertSame('Winter Sale 2025', $sales[0]->name());
    }

    #[Test]
    public function it_splits_a_sale_across_a_gap_in_the_data()
    {
        $sales = Sale::detect($this->records([
            '2025-06-26' => 30,
            '2025-12-20' => 40,
            '2025-12-21' => 2,
        ]));

        $this->assertCount(2, $sales);
        $this->assertSame('2025-06-26', $sales[0]->end->toDateString());
        $this->assertSame('2025-12-20', $sales[1]->start->toDateString());
    }

    #[Test]
    public function it_ignores_small_discounts()
    {
        $this->assertSame([], Sale::detect($this->records([
            '2025-01-01' => 5,
            '2025-01-02' => 19,
        ])));
    }
}
