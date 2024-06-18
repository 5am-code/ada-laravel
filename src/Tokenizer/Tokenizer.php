<?php

namespace Ada\Tokenizer;

abstract class Tokenizer
{
    abstract public function count(string $text): int;
}
