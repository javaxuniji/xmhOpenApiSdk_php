<?php

namespace XMH\OpenApiSdk\Ship;

use XMH\OpenApiSdk\Client;

class Ship
{
    const SyncShipUrl = 'SyncPlatformShip';
    private Client $client;
    public function __construct() {
        $this->client = new Client();
    }
    public function Sync(array $params): array {
        return $this->client->do(self::SyncShipUrl, $params);
    }
}