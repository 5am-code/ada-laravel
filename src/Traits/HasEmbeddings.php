<?php

namespace Ada\Traits;

use Ada\Index\Index;
use Ada\Models\Embedding;

trait HasEmbeddings
{
    public function embed(string $key, string $content): bool
    {
        $index = app()->make(Index::class);

        try {
            $embeddings = $index->getEmbeddableChunks($content);
        } catch (\Exception $e) {
            return false;
        }

        /**
         * @var Embedding $embedding
         */
        // TODO: Yes, that would be better in a queueable job!
        foreach ($embeddings as $embedding) {
            $embedding->key = $key;
            $embedding->embeddable_type = static::class;
            $embedding->embeddable_id = $this->id;

            $embeddingVector = $index->embed($embedding->content);
            $embedding->embedding = $embeddingVector->embeddings;

            $embedding->save();
        }

        return true;
    }

    public function embeddings()
    {
        return $this->morphMany(Embedding::class, 'embeddable');
    }
}
