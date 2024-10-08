<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'original'   => $this->original / 100,
            'sale'       => $this->sale / 100,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
