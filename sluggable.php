<?php

/**
 * Slugify all your Eloquent models with this bundle
 *
 * @package Sluggable
 * @version 1.0
 * @author  Bryan te Beek <bryantebeek@gmail.com>
 * @link    http://github.com/bryantebeek/laravel-bundle-sluggable
 */

class Sluggable
{
    const DEFAULT_FIELD = 'slug';
    const DEFAULT_SEPARATOR = '-';

    public static function slugify($model)
    {
        if (!isset($model::$sluggable)) {
            return;
        }

        $options = $model::$sluggable;

        if (!isset($options['from'])) {
            throw new Exception('You need to define $sluggable[\'from\'] in the '.get_class($model).' model.');
        }

        $to = isset($options['to']) ? $options['to'] : self::DEFAULT_FIELD;
        $from = $options['from'];
        $separator = isset($options['separator']) ? $options['separator'] : self::DEFAULT_SEPARATOR;

        $model->$to = Str::slug($model->$from, $separator);
    }
}