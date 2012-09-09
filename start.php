<?php

/**
 * Slugify all your Eloquent models with this bundle
 *
 * @package Sluggable
 * @version 1.0
 * @author  Bryan te Beek <bryantebeek@gmail.com>
 * @link    http://github.com/bryantebeek/laravel-bundle-sluggable
 */

Autoloader::map(array(
    'Sluggable' => Bundle::path('sluggable').'sluggable.php',
));

Event::listen('eloquent.saving', array('Sluggable', 'slugify'));