<?php

namespace Ada\Engine\Responses;

/**
 * A response object to simplify handling error responses of different
 * engines for a variety of endpoints.
 */
class ErrorResponse extends Response
{
    public string $errorMessage = '';

    public bool $success = false;

    public function getContent(): string
    {
        return "Error: $this->errorMessage";
    }
}
