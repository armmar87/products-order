<?php

namespace App\Services;

use \GuzzleHttp\Client;

final class SlackService
{
    private Client $client;
    private string $webhookUrl = 'https://hooks.slack.com/services/XXX/YYY/ZZZ';

    public function __cunstruct() {
        $this->client = new Client();
    }

    public function send(int $orderId): void
    {
        $this->client->post($this->webhookUrl, [
            'json' => ['text' => 'New order: #' . $orderId]
        ]);
    }
}
