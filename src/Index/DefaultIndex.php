<?php

namespace Ada\Index;

use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Models\Embedding;
use Ada\Tools\Distance\Distance;
use Ada\Tools\Distance\OpenAIDistance;
use Ada\Tools\Prompts\Prompt;
use Ada\Tools\TextSplitter\AdaTextSplitter;
use Ada\Tools\TextSplitter\TextSplitter;
use Exception;

class DefaultIndex extends Index
{
    public function embed(string $content, string $model = 'text-embedding-ada-002', $options = []): EmbeddedResponse|ErrorResponse
    {
        return $this->engine->embed($content, $model, $options);
    }

    public function generate(Prompt $prompt, string $model = 'gpt-3.5-turbo', int $temperature = 0, $options = []): GeneratedResponse|ErrorResponse
    {
        return $this->engine->generate($prompt, $model, $temperature, $options);
    }

    public function getEmbeddableChunks(string $text): array
    {
        $chunks = [];

        try {
            $textChunks = $this->splitter->split($text);
        } catch (Exception) {
            $textChunks = [];
        }

        foreach ($textChunks as $index => $chunk) {
            $chunks[] = new Embedding([
                'content' => $chunk,
            ]);
        }

        return $chunks;
    }

    protected function getDefaultSplitter(): TextSplitter
    {
        return new AdaTextSplitter();
    }

    public function getDistance(): Distance
    {
        return new OpenAIDistance();
    }
}
