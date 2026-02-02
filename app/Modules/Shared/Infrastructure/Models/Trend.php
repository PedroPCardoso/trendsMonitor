<?php

namespace App\Modules\Shared\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class Trend extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
        'fetched_at' => 'datetime',
    ];
}
