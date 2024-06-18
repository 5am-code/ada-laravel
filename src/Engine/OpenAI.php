<?php

namespace Ada\Engine;

use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Tools\Prompts\OpenAIPrompt;
use Ada\Tools\Prompts\Prompt;
use Exception;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;
use Throwable;

class OpenAI extends Engine
{
    protected Client $client;

    public function __construct()
    {
        $this->client = \OpenAI::client(config('ada.client_token'));
    }

    public function embed(string $text, string $model = 'text-embedding-ada-002', $options = []): EmbeddedResponse|ErrorResponse
    {
        try {
            $result = $this->client->embeddings()->create([
                'model' => $model,
                'input' => $text,
                ...$options,
            ]);

            return $this->toEmbeddedResponse($result);
        } catch (Throwable $e) {
            return $this->toErrorResponse($e);
        }
    }

    public function generate(Prompt $prompt, string $model = 'gpt-3.5-turbo', int $temperature = 0, $options = []): GeneratedResponse|ErrorResponse
    {
        try {
            $result = $this->client->chat()->create([
                'model'       => $model,
                'messages'    => $prompt->toArray(),
                'temperature' => $temperature,
                ...$options,
            ]);

            return $this->toGeneratedResponse($result);
        } catch (Throwable $e) {
            return $this->toErrorResponse($e);
        }
    }

    protected function toGeneratedResponse($result): GeneratedResponse|ErrorResponse
    {
        $response = new GeneratedResponse();

        $result = $result->toArray();

        $response->engine = self::class;
        $response->model = $result['model'] ?? '';

        $response->content = $result['choices'][0]['message']['content'];

        $response->tokenUsage = $result['usage'];

        return $response;
    }

    protected function toEmbeddedResponse($result): EmbeddedResponse|ErrorResponse
    {
        $response = new EmbeddedResponse();

        $result = $result->toArray();

        $response->engine = self::class;
        $response->model = $result['model'] ?? '';

        $response->embeddings = $result['data'][0]['embedding'];

        $response->tokenUsage = $result['usage'];

        return $response;
    }

    protected function toErrorResponse(Throwable $exception): ErrorResponse
    {
        $response = new ErrorResponse();

        $response->engine = self::class;
        $response->success = false;

        if ($exception instanceof ErrorException) {
            $response->errorMessage = $exception->getErrorMessage();
        } elseif ($exception instanceof Exception) {
            $response->errorMessage = $exception->getMessage();
        }

        return $response;
    }

    public function getDefaultPrompt(): OpenAIPrompt
    {
        return new OpenAIPrompt();
    }
}
