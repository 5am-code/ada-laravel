    <?php

    return [
        'client_token' => env('ADA_CLIENT_TOKEN'),
        'index_class' => \Ada\Index\DefaultIndex::class,
        'default_prompt_view' => 'ada::default-prompt',

        'distance' => [
            'openai' => \Pgvector\Laravel\Distance::Cosine
        ]
    ];
