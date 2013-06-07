<?php

/**
 * Easy slugging for your Eloquent models.
 *
 * @package Sluggable
 * @version 1.0
 * @author  Colin Viebrock <colin@viebrock.ca>
 * @author  Bryan te Beek <bryantebeek@gmail.com>
 * @link    http://github.com/bryantebeek/laravel-bundle-sluggable
 */


return array(

    /**
     * What attributes do we use to build the slug?
     * This can be a single field, like "name" which will build a slug from:
     *
     *     $model->name;
     *
     * Or it can be an array of fields, like ("name", "company"), which builds a slug from:
     *
     *     $model->name . ' ' . $model->company;
     *
     * If you've defined custom getters in your model, you can use those too,
     * since Eloquent will call them when you request a custom attribute.
     *
     * Defaults to null, which uses the toString() method on your model.
     *
     */
    'build_from' => null,

    /**
     * What field to we store the slug in?  Defaults to "slug".
     * You need to configure this when building the SQL for your database, e.g.:
     *
     * Schema::create('users', function($table)
     * {
     *    $table->string('slug');
     * });
     *
     */
    'save_to' => 'slug',

    /**
     * What style should we use to build the slug?
     *
     * - "slug" uses Laravel's Str::slug()
     *
     * Defaults to "slug" (only option for now).
     */
    'style' => 'slug',

    /**
     * Separator to use. Defaults to a hyphen.
     */
    'separator' => '-',

    /**
     * Enforce uniqueness of slugs? Defaults to true.
     * If a generated slug already exists, an incremental numeric
     * value will be appended to the end until a unique slug is found.  e.g.:
     *
     *     my-slug
     *     my-slug-1
     *     my-slug-2
     */
    'unique' => true,

    /**
     * Whether to update the slug value when a model is being
     * re-saved (i.e. already exists). Defaults to false, which
     * means slugs are not updated.
     */
    'on_update' => false,

);