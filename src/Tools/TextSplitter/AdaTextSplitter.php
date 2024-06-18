<?php

namespace Ada\Tools\TextSplitter;

use Ada\Tokenizer\AdaTokenizer;

class AdaTextSplitter extends TextSplitter
{
    public function setTokenizer()
    {
        $this->tokenizer = new AdaTokenizer();
    }
}
