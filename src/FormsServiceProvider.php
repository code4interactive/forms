<?php

namespace Code4\Forms;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class FormsServiceProvider extends ServiceProvider {

    public function register() {
        $this->app->singleton('forms', function($app) {
            //return new Menu($app['files'], $app['config'], $app['request']);
        });

        $this->registerAliases();
    }

    public function boot()
    {
        //$this->publishes([__DIR__ . '/../config/menu.php' => base_path('config/menu.php')], 'config');
        $this->loadViewsFrom(__DIR__ . '/../views', 'forms');
    }


    private function registerAliases() {
        $aliasLoader = AliasLoader::getInstance();
        $aliasLoader->alias('Forms', Facades\Forms::class);
    }

}