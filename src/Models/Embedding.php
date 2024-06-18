<?php

namespace Ada\Models;

use Ada\Engine\Responses\ErrorResponse;
use Ada\Engine\Responses\GeneratedResponse;
use Ada\Index\Index;
use Ada\Tools\Prompts\Prompt;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\Distance;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class Embedding extends Model
{
    use HasNeighbors;

    protected $guarded = [];

    protected $casts = [
        'embedding' => Vector::class,
    ];

    public function embeddable()
    {
        return $this->morphTo();
    }

    /**
     * @param  string  $query  The query to lookup.
     * @param  Prompt|null  $contextPrompt  The prompt to use for the context, in case a custom template is necessary.
     * @param  Closure|null  $additionalConstraints  Limit the lookup by providing a query.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function lookup(string $query, ?Prompt $contextPrompt = null, ?Closure $additionalConstraints = null): string
    {
        $index = app()->make(Index::class);

        $queryEmbedding = $index->embed($query);

        if ($queryEmbedding instanceof ErrorResponse) {
            return 'Error: '.$queryEmbedding->getMessage();
        }

        $vector = new Vector($queryEmbedding->embeddings);

        $nearestNeighbor = self::getNearestNeighbor($vector, $additionalConstraints);

        $context = $nearestNeighbor->content ?? 'No context given.';

        if ($contextPrompt === null) {
            $contextPrompt = $index->engine->getDefaultPrompt();
        }

        $prompt = $contextPrompt
            ->replaceInTemplate('context', $context)
            ->setQuery($query);

        /** @var GeneratedResponse $response */
        $response = $index->generate($prompt);

        return $response->getContent();
    }

    /**
     * @param  Vector  $vector  The vector to compare to
     * @param  Closure|null  $additionalConstraints  Limit the search further by providing a query.
     */
    public static function getNearestNeighbor(Vector $vector, ?Closure $additionalConstraints = null): ?Embedding
    {
        $query = Embedding::query();

        if ($additionalConstraints !== null) {
            $additionalConstraints($query);
        }

        try {
            return $query->nearestNeighbors('embedding', $vector->toArray(), Distance::Cosine)
                ->get()
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
