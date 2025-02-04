<?php

namespace Ada\Traits;

use Ada\Index\Index;
use Ada\Jobs\EmbedJob;
use Ada\Models\Embedding;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
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
        foreach ($embeddings as $embedding) {
            $embedding->key = $key;
            $embedding->embeddable_type = static::class;
            $embedding->embeddable_id = $this->getKey();

            $embedding->save();

            dispatch(new EmbedJob($embedding));
        }

        return true;
    }

    public function embeddings()
    {
        return $this->morphMany(Embedding::class, 'embeddable');
    }
}
