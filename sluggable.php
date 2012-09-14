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
     * @var null|\Laravel\Database\Eloquent\Model The model that is going to be sluggified is stored here.
     */
    private $model = null;
    /**
     * @var null|array The configuration array is stored here for internal use.
     */
    private $configuration = null;

    /**
     * @param  \Laravel\Database\Eloquent\Model      $model   The model that needs to be sluggified.
     * @param bool                                   $force   Force the model to generate a new slug, usefull if 'on_update' needs to bypassed. Defaults to false.
     * @param  array                                 $objects An array of {@link \Laravel\Database\Eloquent\Model} objects which the slug should be tested against, this defaults to null in which case Sluggable will check the database to determine the slug.
     */
    public static function make($model, $force = false, $objects = null)
    {
        $class = get_class($model);

        // If the class has no sluggable configuration, there is nothing to be done so we stop.
        if (!isset($class::$sluggable))
        {
            return;
        }

        $instance = new Sluggable($model);

        // Check if the model is going to be created or updated, if it's going to update
        // and 'on_update' is false we can stop, unless we forced the update with the force parameter.
        if (($model->exists && $instance->configuration['on_update'] == false) && !$force)
        {
            return;
        }

        // Create the slug
        $slug = $instance->slug();

        // Check if the slug is already present and weither we want to have unique slugs
        if (($object = $instance->exists($slug, $objects)) && $instance->configuration['unique'])
        {
            // If the slug exists, we get the next available slug
            $slug = $instance->next($slug, $object);
        }

        // We set the 'save_to' field of the model to the newly created slug, yeah!
        $model->{$instance->configuration['save_to']} = $slug;
    }

    /**
     * The constructor for Sluggable
     *
     * @param $model
     */
    protected function __construct($model)
    {
        $this->model = $model;
        $this->configure();
    }

    /**
     * This functions load our configuration in a few steps:
     *
     * 1. We get the configuration from the model
     * 2. We get the 'sluggable' configuration from the main application
     * 3. We get the 'sluggable' configuration from our bundle
     *
     * Then we merge the configurations and save them for later use.
     */
    protected function configure()
    {
        $class = get_class($this->model);
        $model_configuration = $class::$sluggable;
        $default_configuration = Config::get('sluggable', Config::get('sluggable::sluggable', array()));

        $this->configuration = array_merge($default_configuration, $model_configuration);
    }

    /**
     * Creates the actual slug from the 'build_from' field using the 'separator' configuration.
     *
     * @return string
     */
    protected function slug()
    {
        $string = $this->model->{$this->configuration['build_from']};
        $separator = $this->configuration['separator'];
        return Str::slug($string, $separator);
    }

    /**
     * @param string $slug The slug which we are going to determine the existence of
     * @param \Laravel\Database\Eloquent\Model $objects An array of {@link \Laravel\Database\Eloquent\Model} which we are going to test our slug against to see if it already exists.
     *
     * @return mixed If an object exists with the same slug, return it, otherwise return false.
     */
    protected function exists($slug, $objects = null)
    {
        $class = get_class($this->model);

        if (!isset($objects))
        {
            // Get the objects with a slug like our preffered one and choose the one with the highest index prepended.
            $object = $class::where($this->configuration['save_to'], 'LIKE', $slug.'%')->order_by(
                $this->configuration['save_to'],
                'DESC'
            )->first();
            return $object;
        } else
        {
            foreach ($objects as $object)
            {
                if (strstr($object->{$this->configuration['save_to']}, $slug) !== false)
                {
                    return $object;
                }
            }
        }
        return null;
    }

    /**
     * @param string $slug The current slug, which we are going to append an index to.
     * @param \Laravel\Database\Eloquent\Model $object The object with the slug with the highest index
     *
     * @return string The newly calculated slug
     */
    protected function next($slug, $object)
    {
        $idx = substr($object->{$this->configuration['save_to']}, strlen($slug));
        $idx = ltrim($idx, $this->configuration['separator']);
        $idx = intval($idx);
        $idx++;

        return $slug .= $this->configuration['separator'].$idx;
    }
}