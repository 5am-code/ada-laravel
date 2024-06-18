<?php

namespace Ada\Tools\TextSplitter;

use Exception;
use InvalidArgumentException;

abstract class TextSplitter
{
    protected $tokenizer;

    public function __construct(protected string $separator = ' ', protected int $chunkSize = 4000, protected int $chunkOverlap = 200)
    {
        if ($this->chunkOverlap >= $this->chunkSize) {
            throw new InvalidArgumentException('The chunk overlap must be smaller than the chunk size.');
        }

        $this->setTokenizer();
    }

    abstract public function setTokenizer();

    /**
     * Split a text into chunks.
     *
     * @throws Exception
     */
    public function split(string $text): array
    {
        if ($text === '') {
            return [];
        }

        $splits = explode($this->separator, $text);
        $docs = [];
        $currentDocument = [];
        $total = 0;

        foreach ($splits as $split) {
            $numTokens = $this->tokenizer->count($split);
            if ($numTokens > $this->chunkSize) {
                throw new Exception(
                    'A single term is larger than the allowed chunk size.'.
                    'Term size: '.$numTokens.
                    'Chunk size: '.$this->chunkSize
                );
            }
            // If the total tokens in current_doc exceeds the chunk size:
            // 1. Update the docs list
            if ($total + $numTokens > $this->chunkSize) {
                $docs[] = implode($this->separator, $currentDocument);
                // 2. Shrink the current_doc (from the front) until it is gets smaller
                // than the overlap size
                while ($total > $this->chunkOverlap) {
                    $cur_num_tokens = max($this->tokenizer->count($currentDocument[0]), 1);
                    $total -= $cur_num_tokens;
                    array_shift($currentDocument);
                }
                // 3. From here we can continue to build up the current_doc again
            }
            // Build up the current_doc with term d, and update the total counter with
            // the number of the number of tokens in d, wrt self.tokenizer
            $currentDocument[] = $split;
            $total += $numTokens;
        }
        $docs[] = implode($this->separator, $currentDocument);

        return $docs;
    }
}
