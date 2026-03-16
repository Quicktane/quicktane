<?php

declare(strict_types=1);

namespace App\Directory\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $table = 'currency_rates';

    protected $fillable = [
        'base_currency_code',
        'target_currency_code',
        'rate',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'float',
        ];
    }
}
