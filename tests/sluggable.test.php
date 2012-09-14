<?php

include(Bundle::path('sluggable').'sluggable.php');

class Sluggable_Test extends PHPUnit_Framework_TestCase
{
    private $model = null;

    public function setUp() {
        $this->model = new Model();
        $this->model->title = 'This is a test';
        $this->model->other_title_field = 'This is another test';
    }

    public function testSlug()
    {
        Sluggable::make($this->model, false, array());
        $this->assertEquals("this-is-a-test", $this->model->slug);
    }

    public function testExistsWithoutIndex()
    {
        $dummy = new Model();
        $dummy->slug = 'this-is-a-test';

        Sluggable::make($this->model, false, array($dummy));
        $this->assertEquals("this-is-a-test-1", $this->model->slug);
    }

    public function testExistsWithIndex()
    {
        $dummy = new Model();
        $dummy->slug = 'this-is-a-test-5';

        Sluggable::make($this->model, false, array($dummy));
        $this->assertEquals("this-is-a-test-6", $this->model->slug);
    }

    public function testOnUpdateTrue() {
        $class = get_class($this->model);
        $class::$sluggable['on_update'] = true;

        $this->model->exists = true;
        $this->model->slug = 'random';

        Sluggable::make($this->model, false, array());
        $this->assertEquals("this-is-a-test", $this->model->slug);

        $class::$sluggable['on_update'] = null;
    }

    public function testOnUpdateFalse() {
        $class = get_class($this->model);
        $class::$sluggable['on_update'] = false;

        $this->model->exists = true;
        $this->model->slug = 'random';

        Sluggable::make($this->model, false, array());
        $this->assertEquals("random", $this->model->slug);

        $class::$sluggable['on_update'] = null;
    }

    public function testForce() {
        $class = get_class($this->model);
        $class::$sluggable['on_update'] = false;

        $this->model->exists = true;
        $this->model->slug = 'random';

        Sluggable::make($this->model, true, array());
        $this->assertEquals("this-is-a-test", $this->model->slug);
    }

    public function testSaveTo() {
        $class = get_class($this->model);
        $class::$sluggable['save_to'] = 'other_slug_field';

        Sluggable::make($this->model, false, array());
        $this->assertEquals("this-is-a-test", $this->model->other_slug_field);

        $class::$sluggable['save_to'] = 'slug';
    }

    public function testBuildFrom() {
        $class = get_class($this->model);
        $class::$sluggable['build_from'] = 'other_title_field';

        Sluggable::make($this->model, false, array());
        $this->assertEquals("this-is-another-test", $this->model->slug);

        $class::$sluggable['build_from'] = 'title';
    }

    public function testSeparator() {
        $class = get_class($this->model);
        $class::$sluggable['separator'] = '/';

        Sluggable::make($this->model, false, array());
        $this->assertEquals("this/is/a/test", $this->model->slug);

        $class::$sluggable['separator'] = '-';
    }

    public function testNotUnique() {
        $class = get_class($this->model);
        $class::$sluggable['unique'] = false;

        $dummy = new Model();
        $dummy->slug = 'this-is-a-test';

        Sluggable::make($this->model, false, array($dummy));
        $this->assertEquals("this-is-a-test", $this->model->slug);

        $class::$sluggable['unique'] = true;
    }

    public function testMultipleBuildFrom() {
        $class = get_class($this->model);
        $class::$sluggable['build_from'] = array('title', 'other_title_field');

        Sluggable::make($this->model, false, array());
        $this->assertEquals("this-is-a-test-this-is-another-test", $this->model->slug);

        $class::$sluggable['build_from'] = 'title';
    }

    public function tearDown() {
        $this->model = null;
    }
}

class Model extends Eloquent
{
    public static $sluggable = array('build_from' => 'title');
}