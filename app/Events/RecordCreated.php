<?php

namespace App\Events;

use App\Models\Record;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RecordCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Record $record)
    {
    }
}
