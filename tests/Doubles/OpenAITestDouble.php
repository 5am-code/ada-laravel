<?php

namespace Ada\Tests\Doubles;

use Ada\Engine\OpenAI;
use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;

class OpenAITestDouble extends OpenAI
{
    public function publicToEmbeddedResponse($result): EmbeddedResponse|ErrorResponse
    {
        return $this->toEmbeddedResponse($result);
    }

    public function publicToGeneratedResponse($result): GeneratedResponse|ErrorResponse
    {
        return $this->toGeneratedResponse($result);
    }

    public function publicToErrorResponse(\Throwable $exception): ErrorResponse
    {
        return $this->toErrorResponse($exception);
    }
}
