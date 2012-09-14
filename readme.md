#Sluggable bundle for Laravel by Bryan te Beek

This bundle helps you to easily generate slugs for your Eloquent models.


##Installation

You can install Sluggable by using Artisan CLI:
```php
php artisan bundle:install sluggable
```

Also we need to add Sluggable to our applications 'bundles.php' and auto-load it:
```php
return array(
    ...
    'sluggable' => array('auto' => true),
);
```

##Usage
Sluggable can be configured on each Eloquent model by adding the following code:

```php
class Page extends Eloquent
{
    public static $sluggable = array(
        //This option is required. Sluggable will generate a slug from this field.
        'from' => 'title',
        //Optional, this defaults to 'slug'
        'to' => 'slug',
        //Optional, this defaults to '-'
        'separator' => '-',
    );
}
```