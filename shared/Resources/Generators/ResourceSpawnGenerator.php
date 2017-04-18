<?php namespace Miner\Shared\Resources\Generators;

use Miner\Shared\Resources\Constants;
use Miner\Shared\Resources\ResourceSpawn;
use Miner\Shared\Resources\SubTypes\SubTypeInterface;

class ResourceGenerator
{


    private $propertiesGenerator;

    public function __construct(array $activeResourceSpawns, PropertiesGenerator $propertiesGenerator, SpawnDetailsGenerator $spawnDetailsGenerator)
    {
        $this->activeResourceSpawns = $activeResourceSpawns;
        $this->propertiesGenerator = $propertiesGenerator;
        $this->spawnDetailsGenerator = $spawnDetailsGenerator;
    }

    public function generate()
    {
        // decide type and subtype
        $subType = $this->getNeededSubType();

        // generate properties based on type of resource subtype
        $properties = $this->propertiesGenerator->generateForResourceSubType($subType);

        // generate spawn details
        $spawnDetails = $this->spawnDetailsGenerator->generate();

        // generate and return a resource spawn
        return new ResourceSpawn($properties, $spawnDetails);
    }

    private function getNeededSubType(): SubTypeInterface
    {
        // give weight to resources that are least common
        $subTypeWeights = [];

        foreach(Constants::SUB_TYPES as $subTypeString) {
            $subTypeWeights[$subTypeString] = 10;
        };

        foreach($this->activeResourceSpawns as $resourceSpawn) {
            $subTypeWeights[get_class($resourceSpawn)] = $subTypeWeights[get_class($resourceSpawn)] - 5;
        }

        $lottery = [];

        // subtypes with higher weights should be more likely to win, so we give them more tickets to the lottery
        foreach($subTypeWeights as $subTypeString => $tickets) {
            for($i = 0; $i < $tickets; $i++) {
                $lottery[] = $subTypeString;
            }
        }

        $winningIndex = rand(0, count($lottery));

        return new $lottery[$winningIndex];
    }
}