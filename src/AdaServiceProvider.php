<?php

namespace Ada;

use Ada\Engine\Engine;
use Ada\Engine\OpenAI;
use Ada\Index\Index;
use Exception;
use Illuminate\Support\ServiceProvider;
use ReflectionException;

class AdaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/prompts', 'ada');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ada.php' => config_path('ada.php'),
            ], 'ada-config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ada.php', 'ada');

        $this->publishes([
            __DIR__.'/../config/ada.php' => config_path('ada.php'),
        ], 'ada-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'ada-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/ada'),
        ], 'ada-views');

        $this->app->singleton('ada', function () {
            return new Ada();
        });

        $this->validateIndexClass();

        $this->app->bind(Index::class, config('ada.index_class'));
        $this->app->bind(Engine::class, OpenAI::class);
    }

    /**
     * Checks if the index class from configuration implements the Ada Index interface.
     *
     * @throws ReflectionException
     */
    protected function validateIndexClass()
    {
        $indexClass = config('ada.index_class', \Ada\Index\DefaultIndex::class);
        $reflection = new \ReflectionClass($indexClass);

        if (!$reflection->isSubclassOf(Index::class)) {
            throw new Exception("Index class has to implement \Ada\Index\Index.");
        }
    }
}
