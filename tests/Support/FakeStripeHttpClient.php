<?php

namespace Tests\Support;

use Stripe\HttpClient\ClientInterface;

class FakeStripeHttpClient implements ClientInterface
{
    protected array $queue = [];

    public array $requests = [];

    public function queue(string $method, string $urlContains, array $body, int $code = 200): static
    {
        $this->queue[] = [
            'method' => strtolower($method),
            'urlContains' => $urlContains,
            'body' => $body,
            'code' => $code,
        ];

        return $this;
    }

    public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1')
    {
        $this->requests[] = ['method' => $method, 'url' => $absUrl, 'params' => $params];

        foreach ($this->queue as $index => $response) {
            if ($response['method'] === strtolower($method) && str_contains($absUrl, $response['urlContains'])) {
                unset($this->queue[$index]);

                return [json_encode($response['body']), $response['code'], []];
            }
        }

        throw new \RuntimeException("No fake Stripe response queued for {$method} {$absUrl}");
    }
}
