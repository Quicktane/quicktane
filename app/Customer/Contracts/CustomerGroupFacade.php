<?php

declare(strict_types=1);

namespace App\Customer\Contracts;

use App\Customer\Models\CustomerGroup;
use Illuminate\Support\Collection;

interface CustomerGroupFacade
{
    public function findGroup(int $id): ?CustomerGroup;

    public function findGroupByCode(string $code): ?CustomerGroup;

    public function getDefaultGroup(): CustomerGroup;

    public function listGroups(): Collection;
}
