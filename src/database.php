<?php
declare(strict_types=1);
namespace Root\Garden;

use Root\Garden\Collections;
use MongoDB;

class DatabaseConnection {
    private MongoDB\Database $client;
    public Collections\Collection $seeds;
    public Collections\Collection $plantings;
    public Collections\Collection $logs;
    public Collections\Collection $beds;

    public function __construct(array $options) {
        $client = new MongoDB\Client("mongodb://$options[hostname]:$options[port]");
        $this->client = $client->{$options['database']};

        $this->seeds = new Collections\SeedCollection($this);
        $this->plantings = new Collections\PlantingCollection($this);
        $this->logs = new Collections\LogCollection($this);
        $this->beds = new Collections\BedCollection($this);
    }

    public function get_mongodb_collection(string $collection): MongoDB\Collection {
        return $this->client->$collection;
    }
}
