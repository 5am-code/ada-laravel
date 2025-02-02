<?php

namespace Ada\Tools\TextSplitter;

use Ada\Tokenizer\AdaTokenizer;

/**
 * TextSplitter implementation for the `text-embeddings-002` model of Open AI.
 */
class AdaTextSplitter extends TextSplitter
{
    public function setTokenizer()
    {
        $this->tokenizer = new AdaTokenizer();
    }
}
