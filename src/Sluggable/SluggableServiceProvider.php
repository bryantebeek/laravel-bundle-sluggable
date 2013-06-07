<?php

namespace Sluggable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;

class SluggableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->package('sluggable', 'sluggable', realpath(__DIR__.'/../'));

        /**
         * Listen for save events on all models
         */
        Event::listen('eloquent.saving: *', function ($model, $event) {
            Sluggable::make($model);
        });
    }

    public function register()
    {
        //
    }

    public function provides()
    {
        return array('sluggable');
    }
}
