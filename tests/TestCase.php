<?php

namespace Gmory\Laranotes\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
	 * Setup the test environment.
	 */
	public function setUp()
	{
	    parent::setUp();
	    $this->createPostTable();
	    $this->loadLaravelMigrations(['--database' => 'test']);
	}

	/**
	 * Define environment setup.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function getEnvironmentSetUp($app)
	{
	    // Setup default database to use sqlite :memory:
	    $app['config']->set('database.default', 'test');
	    $app['config']->set('database.connections.test', [
	        'driver'   => 'sqlite',
	        'database' => ':memory:',
	    ]);
	}

	/**
     * Sets the package providers.
     *
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['\Gmory\Laranotes\LaranotesServiceProvider'];
    }

    /**
     * Create the posts table.
     * Since this is not a migration handled by DatabaseMigrations, do this manually.
     */
    protected function createPostTable()
    {
    	if($this->app['db']->connection()->getSchemaBuilder()->hasTable('posts'))
    		$this->app['db']->connection()->getSchemaBuilder()->drop('posts');

        $this->app['db']->connection()->getSchemaBuilder()->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('text')->nullable();
            $table->timestamps();
        });
    }
}