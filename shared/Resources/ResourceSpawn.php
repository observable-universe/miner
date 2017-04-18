<?php namespace Miner\Shared\Resources;

class ResourceSpawn
{
    private $overallQuality;

    private $contuctivity;

    private $durability;

    private $seed;

    private $top;

    private $maxYield;

    private $spawnsAt;

    private $expiresAt;

    public function __construct($properties, $spawnDetails)
    {
        $this->properties = $properties;

        $this->spawnDetails = $spawnDetails;
    }
}


