<?php

namespace App\Services\News\Writing;

use Anthropic\Client;

/** Single place that builds the Anthropic SDK client (swapped for a fake in tests). */
class ClaudeClientFactory
{
    /** @return Client */
    public function make(string $apiKey)
    {
        // A long article with reasoning can take minutes; the SDK retries transient errors itself.
        return new Client(apiKey: $apiKey, requestOptions: ['timeout' => 300]);
    }
}
