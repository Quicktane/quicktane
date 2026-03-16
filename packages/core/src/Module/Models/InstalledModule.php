<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Models;

use Illuminate\Database\Eloquent\Model;

class InstalledModule extends Model
{
    public $timestamps = false;

    protected $table = 'module_versions';

    protected $fillable = [
        'name',
        'version',
        'installed_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
