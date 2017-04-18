<?php namespace Miner\Shared\Resources\SubTypes;

use Miner\Shared\Resources\Properties\Conductivity;
use Miner\Shared\Resources\Properties\Durability;
use Miner\Shared\Resources\Properties\OverallQuality;

class Silver implements SubTypeInterface
{
    public function propertiesAffectedBy(): array
    {
        return [
            OverallQuality::class => 1,
            Conductivity::class => 1.5,
            Durability::class => .75
        ];
    }
}