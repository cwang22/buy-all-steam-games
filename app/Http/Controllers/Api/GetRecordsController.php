<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RecordResource;
use App\Models\Record;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetRecordsController
{
    public function __invoke(): ResourceCollection
    {
        return RecordResource::collection(Record::latest()->get());
    }
}