<?php namespace Miner\Shared\Resources;

use Miner\Shared\Resources\Properties\Conductivity;
use Miner\Shared\Resources\Properties\Durability;
use Miner\Shared\Resources\Properties\OverallQuality;
use Miner\Shared\Resources\SubTypes\Gold;
use Miner\Shared\Resources\SubTypes\Silver;
use Miner\Shared\Resources\SubTypes\Steel;
use Miner\Shared\Resources\Types\Energy;
use Miner\Shared\Resources\Types\MetalOre;
use Miner\Shared\Resources\Types\Polymer;

class Constants
{
    const TYPES = [
        Energy::class,
        MetalOre::class,
        Polymer::class
    ];

    const SUB_TYPES = [
        Gold::class,
        Silver::class,
        Steel::class
    ];

    const PROPERTIES = [
        Conductivity::class,
        Durability::class,
        OverallQuality::class
    ];
}
