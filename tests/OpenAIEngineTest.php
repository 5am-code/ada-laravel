<?php

use Ada\Engine\Responses\EmbeddedResponse;
use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Tests\Doubles\OpenAITestDouble;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('ada.client_token', 'test_token');

    $this->engine = new OpenAITestDouble();
});

it('creates an EmbeddedResponse from a Open AI JSON embedding response', function () {
    $response = json_decode(file_get_contents('tests/fixtures/openai_200_embed.json'), true);

    $resultMock = Mockery::mock(Result::class);
    $resultMock->shouldReceive('toArray')->andReturn($response);

    $response = $this->engine->publicToEmbeddedResponse($resultMock);

    expect($response)->toBeInstanceOf(EmbeddedResponse::class)
        ->and($response->engine)->toBe(\Ada\Engine\OpenAI::class)
        ->and($response->success)->toBeTrue()
        ->and($response->model)->toBe('text-embedding-ada-002')
        ->and($response->getContent())->toBe(json_encode([-0.025455512, 0.004357308, -0.023832073]))
        ->and($response->tokenUsage)->toBe([
            'prompt_tokens' => 41,
            'total_tokens' => 41,
        ]);
});


it('creates an GeneratedResponse from a Open AI JSON chat response', function () {
    $response = json_decode(file_get_contents('tests/fixtures/openai_200_query.json'), true);

    $resultMock = Mockery::mock(Result::class);
    $resultMock->shouldReceive('toArray')->andReturn($response);

    $response = $this->engine->publicToGeneratedResponse($resultMock);

    expect($response)->toBeInstanceOf(GeneratedResponse::class)
        ->and($response->engine)->toBe(\Ada\Engine\OpenAI::class)
        ->and($response->success)->toBeTrue()
        ->and($response->model)->toBe('gpt-3.5-turbo-0125')
        ->and($response->getContent())->toStartWith("The habitat of the PHP Elephant is referred to as 'Silicon Forests'.")
        ->and($response->tokenUsage)->toBe([
            'prompt_tokens' => 13,
            'completion_tokens' => 9,
            'total_tokens' => 22,
        ]);
});

it('creates an ErrorResponse from a failed Open AI request', function () {

    $exceptionContent = json_decode(file_get_contents('tests/fixtures/openai_failed.json'), true);
    $exception = new \OpenAI\Exceptions\ErrorException($exceptionContent, 500);

    $response = $this->engine->publicToErrorResponse($exception);

    expect($response)->toBeInstanceOf(ErrorResponse::class)
        ->and($response->engine)->toBe(\Ada\Engine\OpenAI::class)
        ->and($response->success)->toBeFalse()
        ->and($response->getContent())->toStartWith('Error: Incorrect API key provided: abc.');


});
