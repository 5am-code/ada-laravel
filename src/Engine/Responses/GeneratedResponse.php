<?php

namespace Ada\Engine\Responses;

/**
 * A general response object to simplify handling responses of different
 * engines for chat/generate endpoints that will return text.
 */
class GeneratedResponse extends Response
{
    public string $content;

    public bool $success = true;

    public function getContent(): string
    {
        return $this->content;
    }
}
