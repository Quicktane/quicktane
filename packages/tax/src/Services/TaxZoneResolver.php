<?php

declare(strict_types=1);

namespace Quicktane\Tax\Services;

use Quicktane\Tax\Models\TaxZone;
use Quicktane\Tax\Repositories\TaxZoneRepository;

class TaxZoneResolver
{
    public function __construct(
        private readonly TaxZoneRepository $taxZoneRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $address
     */
    public function resolve(array $address): ?TaxZone
    {
        $countryId = (int) ($address['country_id'] ?? 0);
        $regionId = isset($address['region_id']) ? (int) $address['region_id'] : null;
        $postcode = $address['postcode'] ?? null;

        if ($countryId === 0) {
            return null;
        }

        return $this->taxZoneRepository->findByAddress($countryId, $regionId, $postcode);
    }
}
