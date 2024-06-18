<?php

namespace Ada\Index;

use Ada\Engine\Engine;
use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Tools\Prompts\Prompt;
use Ada\Tools\TextSplitter\TextSplitter;

abstract class Index
{
    public Engine $engine;

    protected TextSplitter $splitter;

    public function __construct(Engine $engine, ?TextSplitter $splitter = null)
    {
        $this->engine = $engine;

        if (is_null($splitter)) {
            $this->splitter = $this->getDefaultSplitter();
        }
    }

    abstract protected function getDefaultSplitter(): TextSplitter;

    abstract public function getEmbeddableChunks(string $text): array;

    abstract public function embed(string $content, string $model = 'embedding-model', $options = []): EmbeddedResponse|ErrorResponse;

    abstract public function generate(Prompt $prompt, string $model = 'gpt-3.5-turbo', int $temperature = 0, $options = []): GeneratedResponse|ErrorResponse;
}
