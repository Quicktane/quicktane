<?php

declare(strict_types=1);

namespace App\Store\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $table = 'configurations';

    protected $fillable = [
        'scope',
        'scope_id',
        'path',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'scope_id' => 'integer',
        ];
    }
}
