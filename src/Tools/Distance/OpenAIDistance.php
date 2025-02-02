<?php

namespace Ada\Tools\Distance;

/**
 * Contains the neighbor vector distance calculation value for Open AI.
 */
class OpenAIDistance extends Distance
{
    public function getValue(): int
    {
        return config('ada.distance.openai', \Pgvector\Laravel\Distance::Cosine);
    }
}
