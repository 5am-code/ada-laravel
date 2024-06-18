<?php

namespace Ada\Tokenizer;

use Rajentrivedi\TokenizerX\TokenizerX;

class AdaTokenizer extends Tokenizer
{
    public function count(string $text): int
    {
        return TokenizerX::count($text, 'text-embedding-ada-002');
    }
}
