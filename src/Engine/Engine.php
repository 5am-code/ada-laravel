<?php

namespace Ada\Engine;

use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Tools\Prompts\Prompt;

abstract class Engine
{
    abstract public function embed(string $text);

    abstract public function generate(Prompt $prompt);

    abstract protected function toGeneratedResponse($result): GeneratedResponse|ErrorResponse;

    abstract protected function toEmbeddedResponse($result): EmbeddedResponse|ErrorResponse;

    abstract public function getDefaultPrompt(): Prompt;
}
