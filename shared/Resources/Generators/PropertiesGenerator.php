<?php namespace Miner\Shared\Resources\Generators;

use Miner\Shared\Resources\Properties\Conductivity;
use Miner\Shared\Resources\Properties\Durability;
use Miner\Shared\Resources\Properties\OverallQuality;
use Miner\Shared\Resources\SubTypes\SubTypeInterface;
use Minter\Shared\Resources\Properties;

class PropertiesGenerator
{
    const MIN = 1;

    const MAX = 999;

    public function generateForResourceSubType(SubTypeInterface $subType): Properties
    {
        $subTypeModifiers = $subType->propertiesAffectedBy();

        $overallQuality = $this->generateProperty($subTypeModifiers[OverallQuality::class]);
        $conductivity = $this->generateProperty($subTypeModifiers[Conductivity::class]);
        $durability = $this->generateProperty( $subTypeModifiers[Durability::class]);

        return new Properties($overallQuality, $conductivity, $durability);
    }

    private function generateProperty($weight)
    {
        $offset = self::MAX - self::MIN + 1;

        return floor(self::MIN + pow(lcg_value(), $weight) * $offset);
    }
}