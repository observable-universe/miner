<?php namespace Miner\Shared\Resources\Generators;

use Miner\Shared\Resources\SpawnDetails;

class SpawnDetailsGenerator
{
    const MIN_YIELD = 100;
    const MAX_YIELD = 500;

    public function generate(): SpawnDetails
    {
        return new SpawnDetails();
    }
}