<?php

namespace Tests\Support\News;

use App\Services\News\Writing\ClaudeClientFactory;
use Closure;
use stdClass;

/** Stand-in for the Anthropic SDK client: records the request and answers with whatever the test builds. */
class FakeClaudeClients
{
    /** @return array{0: ClaudeClientFactory, 1: stdClass} factory plus a recorder exposing ->args and ->countArgs */
    public static function make(?Closure $respond = null, ?Closure $countTokens = null): array
    {
        $recorder = new stdClass;
        $recorder->args = null;
        $recorder->countArgs = null;

        $messages = new class($respond, $countTokens, $recorder)
        {
            public function __construct(private ?Closure $respond, private ?Closure $count, private stdClass $recorder) {}

            public function create(...$args)
            {
                $this->recorder->args = $args;

                return $this->respond ? ($this->respond)($args) : FakeClaudeClients::reply(NewsFixtures::payload());
            }

            public function countTokens(...$args)
            {
                $this->recorder->countArgs = $args;

                return $this->count ? ($this->count)($args) : (object) ['inputTokens' => 8];
            }
        };

        $factory = new class($messages) extends ClaudeClientFactory
        {
            public function __construct(private object $messages) {}

            public function make(string $apiKey)
            {
                return (object) ['messages' => $this->messages];
            }
        };

        return [$factory, $recorder];
    }

    public static function reply(array $payload, string $stop = 'end_turn'): object
    {
        return (object) [
            'stopReason' => $stop,
            'stopDetails' => null,
            'content' => [(object) ['type' => 'thinking'], (object) ['type' => 'text', 'text' => json_encode($payload)]],
        ];
    }
}
