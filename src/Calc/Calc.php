<?php

namespace XMH\OpenApiSdk\Calc;

use XMH\OpenApiSdk\Client;

class Calc {
    const CalcUrl = 'CalcPrice';
    private Client $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function calc(array $params): array {
        return $this->client->do(self::CalcUrl, $params);
    }

}