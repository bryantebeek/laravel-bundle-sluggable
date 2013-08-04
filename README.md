# Sluggable

> **Warning!** This package is not maintained anymore as I've moved to Laravel 4 a long time ago.

Easy automatic slug generation for your Eloquent models.

## Installing the Bundle

Install the bundle using Artisan:

```
php artisan bundle::install sluggable
```

Update your `application/bundles.php` file with:

```php
'sluggable' => array( 'auto' => true ),
```

## Updating your Models

Define a public static property `$sluggable` with the definitions
(see [#Configuration] below for details):

```php
class Post extends Eloquent
{

	public static $sluggable = array(
		'build_from' => 'title',
		'save_to'    => 'slug',
	);

}
```

That's it ... your model is now "sluggable"!


## Using the Class

Saving a model is easy:

```php
$post = new Post(array(
	'title'    => 'My Awesome Blog Post'
));

$post->save();
```

And so is retrieving the slug:

```php
echo $post->slug;
```



## Configuration

Configuration was designed to be as flexible as possible.  You can set up
defaults for all of your Eloquent models, and then override those settings
for individual models.

By default, global configuration can be set in the
`application/config/sluggable.php` file.  If a configuration isn't set,
then the bundle defaults from `bundles/sluggable/config/sluggable.php`
are used.  Here is an example configuration, with all the settings shown:

```php
return array(
	'build_from' => null,
	'save_to'    => 'slug',
	'style'      => 'slug',
	'separator'  => '-',
	'unique'     => true,
	'on_update'  => false,
);
```

`build_from` is the field or array of fields from which to build the slug.
Each `$model->field` is contactenated (with space separation) to build the
sluggable string.  This can be model attribues (i.e. fields in the database)
or custom getters.  So, for example, this works:

```php
class Person extends Eloquent {

	public static $sluggable = array(
		'build_from' => 'fullname'
	);

	public function get_fullname() {
		return $this->firstname . ' ' . $this->lastname;
	}

}
```

If `build_from` is empty, false or null, then the value of `$model->__toString()`
is used.

`save_to` is the field in your model where the slug is stored.  By default,
this is "slug".  You need to create this column in your table when defining
your schema:

```php
Schema::create('posts', function($table)
{
	$table->increments('id');
	$table->string('title');
	$table->string('body');
	$table->string('slug');
	$table->timestamps();
});
```

`style` defines the method used to turn the sluggable string into a slug.
Right now (version 1.0) the only option is "slug" which uses Laravel's
`Str::slug()` method.

`separator` defines the separator used when building a slug.  Default is a
hyphen.

`unique` is a boolean defining whether slugs should be unique among all
models of the given type.  For example, if you have two blog posts and both
are called "My Blog Post", then they will both sluggify to "my-blog-post"
(when using Sluggable's default settings).  This could be a problem, e.g. if you
use the slug in URLs.

By turning `unique` on, then the second Post model will sluggify to
"my-blog-post-1".  If there is a third post with the same title, it will
sluggify to "my-blog-post-2" and so on.  Each subsequent model will get
an incremental value appended to the end of the slug, ensuring uniqueness.

`on_update` is a boolean.  If it is `false` (the default value), then slugs
will not be updated if a model is resaved (e.g. if you change the title
of your blog post, the slug will remain the same) or the slug value has already
been set.  You can set it to `true` (or manually change the $model->slug value
in your own code) if you want to override this behaviour.

(If you want to manually set the slug value using your model's Sluggable settings,
you can run `Sluggable::make($model, true)`.  The second arguement forces
Sluggable to update the slug field.)


## Credits

The idea for this bundle came from using `actAs Sluggable` from the Doctrine ORM.
