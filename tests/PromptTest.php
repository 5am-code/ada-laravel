<?php

use Ada\Tools\Prompts\OpenAIPrompt;

it('can create an instance from a string', function () {
    $template = 'Example template';
    $prompt = OpenAIPrompt::fromString($template);

    $promptData = $prompt->toArray();

    expect($prompt)->toBeInstanceOf(OpenAIPrompt::class)
        ->and($promptData[0])->toHaveKey('role', 'system')
        ->and($promptData[0])->toHaveKey('content', 'Example template')
        ->and($promptData[1])->toHaveKey('role', 'user')
        ->and($promptData[1])->toHaveKey('content', '');
});

it('can set and get a template', function () {
    $template = 'New template';
    $prompt = new OpenAIPrompt();
    $prompt->setTemplate($template);

    $promptData = $prompt->toArray();

    expect($prompt)->toBeInstanceOf(OpenAIPrompt::class)
        ->and($promptData[0])->toHaveKey('role', 'system')
        ->and($promptData[0])->toHaveKey('content', 'New template');
});

it('can set a template from a file', function () {
    $defaultPromptPath = view('ada::default-prompt');

    $prompt = OpenAIPrompt::fromFile($defaultPromptPath->getPath());
    $promptData = $prompt->toArray();

    expect($promptData[0]['content'])->toContain('You are a bot that helps answering questions based on the context information you get each time.');
});

it('replaces placeholders in the template', function () {
    $template = 'Hello, {name}!';
    $prompt = new OpenAIPrompt();
    $prompt->setTemplate($template);
    $prompt->replaceInTemplate('name', 'Ada');

    $promptData = $prompt->toArray();

    expect($promptData[0])->toHaveKey('content', 'Hello, Ada!');
});

it('can set and include a query', function () {
    $query = 'Who was Ada Lovelace?';
    $prompt = new OpenAIPrompt();
    $prompt->setQuery($query);

    $promptData = $prompt->toArray();

    expect($promptData[1])->toHaveKey('content', $query);
});
