<?php

/**
 * Easily slugify all your Eloquent models
 *
 * @package Sluggable
 * @version 1.0
 * @author  Colin Viebrock <colin@viebrock.ca>
 * @author  Bryan te Beek <bryantebeek@gmail.com>
 * @link    http://github.com/bryantebeek/laravel-bundle-sluggable
 */

Autoloader::map(
    array('Sluggable' => __DIR__ . DS . 'sluggable.php'
));

// Listen to the Eloquent save event so we can sluggify on the fly
Event::listen('eloquent.saving', array('Sluggable', 'make'));