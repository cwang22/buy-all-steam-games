<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecordResource;
use App\Models\Record;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GetRecordsController
{
    public function __invoke(Request $request): JsonResponse
    {
        $interval = $request->query('interval') === 'week' ? 'week' : 'day';

        // Keying on the latest id makes a newly fetched record show up straight
        // away instead of waiting for the cached copy to expire.
        $latestId = Record::where(['cc' => 'US'])->max('id');

        $data = Cache::remember("records.{$interval}.{$latestId}", now()->addDay(), function () use ($interval) {
            return RecordResource::collection($this->records($interval))->resolve();
        });

        return response()->json(['data' => $data]);
    }

    private function records(string $interval): Collection
    {
        $records = Record::where(['cc' => 'US'])->orderBy('created_at')->get();

        if ($interval === 'week') {
            // Keep each week's cheapest record so Steam sales, some lasting a
            // single day, still show up as dips.
            $records = $records
                ->groupBy(fn (Record $record) => $record->created_at->format('o-W'))
                ->map(fn ($week) => $week->sortBy('sale')->first())
                ->values();
        }

        return $records;
    }
}
