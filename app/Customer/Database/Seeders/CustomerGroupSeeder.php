<?php

declare(strict_types=1);

namespace App\Customer\Database\Seeders;

use App\Customer\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => 'general', 'name' => 'General', 'is_default' => true, 'sort_order' => 0],
            ['code' => 'wholesale', 'name' => 'Wholesale', 'is_default' => false, 'sort_order' => 1],
            ['code' => 'vip', 'name' => 'VIP', 'is_default' => false, 'sort_order' => 2],
        ];

        foreach ($groups as $groupData) {
            CustomerGroup::firstOrCreate(
                ['code' => $groupData['code']],
                $groupData,
            );
        }
    }
}
