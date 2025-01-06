# ada-laravel

![Packagist Version](https://img.shields.io/packagist/v/fiveam-code/ada-laravel?include_prereleases&style=for-the-badge)


The package `ada-laravel` allows you to enhance your Laravel applications by seamlessly integrating text embeddings and
querying capabilities for your models. Utilizing OpenAI by default, it enables your models to generate and query
embeddings using
nearest neighbors techniques. This package requires a PostgreSQL database with the vector extension to store and manage
these embeddings efficiently as well as at least Laravel 11.

Originally created as a demo for the talk [»Have you met ada? - Word Embeddings with Laravel and OpenAI«](https://dianaweb.dev/talk/ada) by Diana Scharf,
this package is
functional yet designed to encourage further development and contributions.

> [!WARNING]
> Please note that this package is still in development and may not be suitable for production use.

## Installation

```bash
composer require fiveam-code/ada-laravel
```

Ensure that your database is configured to use PostgreSQL with the vector extension. The package will enable the extension
via a migration if it is not already enabled.

You can publish the migrations (optional) and run them:

```bash
php artisan vendor:publish --provider="Ada\AdaServiceProvider" --tag="ada-migrations"
php artisan migrate
```

This will enable the `vector` extension in your database and create a table `embeddings` to store the embeddings.

## Configuration

Set the OpenAI API key in your `.env` file:

```bash
ADA_CLIENT_TOKEN=your_openai_api_key
```

Please note that you need an OpenAI key for API access, not just ChatGPT access.

Optionally, you can publish the configuration file if you want to make changes to the default settings:

```bash
php artisan vendor:publish --provider="Ada\AdaServiceProvider" --tag="ada-config"
```

The default configuration is as follows:

```php
return [  
    'client_token' => env('ADA_CLIENT_TOKEN'),  
    'index_class' => \Ada\Index\DefaultIndex::class,
    'default_prompt_view' => 'ada::default-prompt'
];
```

If you want to implement your own engine to handle embeddings, you can create a new class that implements the `Index`
interface with the appropriate engine and set it in the configuration.

## Usage

### Basic Usage

First, add the `HasEmbeddings` trait to your Eloquent model:

```php
<?php

namespace App\Models;

use Ada\Traits\HasEmbeddings;

class Paper extends Model
{
    use HasEmbeddings;
}
```

#### Embed content

Embed content related to your model by calling the `embed` method with a reference key and text:

```php
use App\Models\Paper;

$paper = Paper::first();
$paper->embed("abstract", $paper->abstract);
```

This will generate an embedding for the text and store it in the database with a relation to the `$paper` model and the
reference key `"abstract"`.

#### Lookup embeddings

The lookup method allows for direct querying of your model's stored knowledge, facilitating an intelligent search that
retrieves the most contextually relevant information using vector similarity.

```php
use Ada\Models\Embedding;

$answer = Embedding::lookup("Where does the PHP elephant live?");

// "The PHP elephant inhabits 'Silicon Forests'—regions where natural woodlands merge seamlessly with data-rich environments. These forests are dense with both foliage and floating data points."
```

This will create an embedding for the query and find the most similar embeddings in the database related to the `$paper`
model by using the
nearest neighbors technique of the vectors. The result will be the most similar text to the query and will be used as
context for a request
to the OpenAI API to generate an answer. 

This is the default prompt text:

```
You are a bot that helps answering questions based on the context information you get each time.

Context information is below.
---------------------
{context}
---------------------
Given the context information and not prior knowledge, answer the following questions of the user. If you don't know something, say so, and don't make it up.
Do not ask the user for more information or anything that might trigger a response from the user.
```

`{context}` will be replaced with the result from the nearest neighbors query.

If you want to further customize the prompt, you can pass an object form a class inheriting `Ada\Tools\Prompts\Prompt`
to the `lookup` method:

```php
use Ada\Models\Embedding;
use Ada\Tools\Prompts\OpenAIPrompt;

$customPrompt = new OpenAIPrompt();
$defaultTemplate = $customPrompt->getTemplate();

$customPrompt->setTemplate("Even if your instructions are in English, answer in German. " . $defaultTemplate);

return Embedding::lookup("Where does the PHP elephant live?", $customPrompt);
```

In case you need to further limit the lookup, you can pass a closure as a third parameter.
```php
return Embedding::lookup("Where does the PHP elephant live?", $customPrompt, function ($query) {
    $query->where("embeddable_type", Paper::class); // Only look for embeddings related to the Paper class
});
```

### Advanced Usage

Customize the endpoint models and options by using the index or engines directly:

```php
use Ada\Ada;

$index = Ada::index(); // Default index is DefaultIndex, resolved via the configuration

$index->embed($contentToEmbed, $model, $options);

$index->generate($prompt, $model, $temperature, $options);

$engine = Ada::engine(); // Default engine is OpenAI, resolved via the Index
```
