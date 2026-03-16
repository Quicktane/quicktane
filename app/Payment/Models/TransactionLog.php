<?php

declare(strict_types=1);

namespace App\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLog extends Model
{
    public $timestamps = false;

    protected $table = 'transaction_logs';

    protected $fillable = [
        'transaction_id',
        'action',
        'status',
        'request_data',
        'response_data',
        'error_message',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'request_data' => 'array',
            'response_data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
