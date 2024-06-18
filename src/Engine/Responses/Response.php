<?php

namespace Ada\Engine\Responses;

/**
 * A general response object to simplify the different engine responses.
 */
abstract class Response
{
    public array $tokenUsage;

    public string $model;

    public string $engine;

    public array $meta = [];

    public bool $success;


    abstract public function getContent(): string;
}
