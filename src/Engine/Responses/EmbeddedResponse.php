<?php

namespace Ada\Engine\Responses;

/**
 * A general response object to simplify handling responses of different
 * engines for embedding endpoints that will return a vector in array form.
 */
class EmbeddedResponse extends Response
{
    public array $embeddings;

    public bool $success = true;

    public function getContent(): string
    {
        return json_encode($this->embeddings);
    }
}
