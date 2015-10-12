<?php

abstract class LavandaDBTestCase extends LavandaTestCase
{
    private static $migrate = true;

    public function setUp()
    {
        parent::setUp();
        $factory = $this->app->make('Illuminate\Database\Eloquent\Factory');
        include('factories.php');
        if(self::$migrate)
        {
            $this->artisan('migrate', [
                '--database' => 'test',
                '--realpath' => realpath(__DIR__.'/migrations'),
            ]);
            self::$migrate = false;
        }
    }

    protected function rollback()
    {
        self::$migrate = true;
        $this->artisan('migrate:rollback');
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('lavanda.model_path', 'tests/models');
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver'    => env('DB_DRIVER', 'mysql'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_DATABASE', 'forge'),
            'username'  => env('DB_USERNAME', 'forge'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false]);
    }
}
