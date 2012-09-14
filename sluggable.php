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
class Sluggable
{
    /**
     * Method that gets fired when the eloquent model is saved.
     * Handles the slugging.
     *
     * @param  Model   $model
     *
     * @return bool
     */
    public static function make($model, $force = false)
    {
        // skip if the model isn't sluggable
        if (!isset($model::$sluggable))
        {
            return true;
        }

        $model_config = $model::$sluggable;

        // read the default config:
        // 1. application/config/sluggable.php
        // 2. bundles/sluggable/config/sluggable.php

        $default_config = Config::get('sluggable', Config::get('sluggable::sluggable', array()));

        $config = array_merge($default_config, $model_config);

        // nicer variables for readability
        $build_from = $save_to = $style = $separator = $unique = $on_update = null;
        extract($config, EXTR_IF_EXISTS);

        // skip slug generation if the model exists or the slug field is already populated,
        // and on_update is false ... unless we are forcing things!
        if (!$force)
        {
            if (($model->exists || !empty($model->{$save_to})) && !$on_update)
            {
                return true;
            }
        }

        // build the slug string
        if ($build_from)
        {
            if (!is_array($build_from))
            {
                $build_from = array($build_from);
            }
            $string = '';
            foreach ($build_from as $field)
            {
                $string .= $model->{$field}.' ';
            }
        } else
        {
            $string = $model->__toString();
        }

        $string = trim($string);

        // build slug using given slug style
        switch ($config['style'])
        {
            case 'slug':
            default:
                $slug = Str::slug($string, $config['separator']);
                break;
        }

        // check for uniqueness?
        if ($unique)
        {
            $class = get_class($model);
            $last = $class::where($save_to, 'LIKE', $slug.'%')->order_by($save_to, 'DESC')->first();

            if ($last)
            {
                $idx = substr($last->{$save_to}, strlen($slug));
                $idx = ltrim($idx, $separator);
                $idx = intval($idx);
                $idx++;

                $slug .= $separator.$idx;
            }
        }

        // update the slug field
        $model->{$save_to} = $slug;

        // done!
        return true;
    }
}