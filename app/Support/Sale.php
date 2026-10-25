<?php

namespace App\Support;

use App\Models\Record;
use Carbon\CarbonInterface;

/**
 * A run of consecutive records where the total is well below full price,
 * e.g. the Steam Summer Sale.
 */
class Sale
{
    /** Normal days sit under 5% off and the big sales go past 30%. */
    public const THRESHOLD = 0.2;

    /** A wider gap in the data means the fetch was down, so don't join across it. */
    public const MAX_GAP_DAYS = 7;

    public function __construct(
        public CarbonInterface $start,
        public CarbonInterface $end,
        public int $lowestSale,
        public int $lowestOriginal,
        public float $deepestDiscount,
        public bool $ongoing,
    ) {}

    /**
     * @param  iterable<Record>  $records  ordered by created_at
     * @return Sale[]
     */
    public static function detect(iterable $records): array
    {
        $sales = [];
        $run = [];
        $previous = null;

        foreach ($records as $record) {
            $inSale = self::discountOf($record) >= self::THRESHOLD;
            $afterGap = $previous && $previous->created_at->diffInDays($record->created_at) > self::MAX_GAP_DAYS;

            if ($run && (! $inSale || $afterGap)) {
                $sales[] = self::fromRun($run, ongoing: false);
                $run = [];
            }

            if ($inSale) {
                $run[] = $record;
            }

            $previous = $record;
        }

        if ($run) {
            $sales[] = self::fromRun($run, ongoing: true);
        }

        return $sales;
    }

    public function lowestDiscount(): float
    {
        return 1 - $this->lowestSale / $this->lowestOriginal;
    }

    public function name(): string
    {
        $season = match ($this->start->month) {
            3 => 'Spring Sale :year',
            6, 7 => 'Summer Sale :year',
            11 => 'Autumn Sale :year',
            12 => 'Winter Sale :year',
            default => null,
        };

        return $season
            ? __($season, ['year' => $this->start->year])
            : __('the sale of :date', ['date' => $this->start->translatedFormat(__('F Y'))]);
    }

    private static function discountOf(Record $record): float
    {
        return $record->original > 0 ? 1 - $record->sale / $record->original : 0;
    }

    /**
     * @param  Record[]  $run
     */
    private static function fromRun(array $run, bool $ongoing): self
    {
        $lowest = collect($run)->sortBy('sale')->first();

        return new self(
            start: $run[0]->created_at,
            end: end($run)->created_at,
            lowestSale: $lowest->sale,
            lowestOriginal: $lowest->original,
            deepestDiscount: max(array_map(self::discountOf(...), $run)),
            ongoing: $ongoing,
        );
    }
}
