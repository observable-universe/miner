<?php namespace Miner\Shared\Resources\SubTypes;

use Miner\Shared\Resources\Properties\Conductivity;
use Miner\Shared\Resources\Properties\Durability;
use Miner\Shared\Resources\Properties\OverallQuality;

class Gold implements SubTypeInterface
{
    public function propertiesAffectedBy(): array
    {
        return [
            OverallQuality::class => .5,
            Conductivity::class => 1,
            Durability::class => .75
        ];
    }
}