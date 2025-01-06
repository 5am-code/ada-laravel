<?php

namespace Ada\Jobs;

use Ada\Index\Index;
use Ada\Models\Embedding;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class EmbedJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Embedding $embedding)
    {
    }

    public function handle(): void
    {
        $index = app()->make(Index::class);

        $embeddingVector = $index->embed($this->embedding->content);
        $this->embedding->embedding = $embeddingVector->embeddings;

        $this->embedding->save();
    }
}
