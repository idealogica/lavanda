<?php

abstract class LavandaTestCase extends FormBuilderTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'Kris\LaravelFormBuilder\FormBuilderServiceProvider',
            'Lavanda\LavandaServiceProvider'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->make('Illuminate\Contracts\Http\Kernel')->
            pushMiddleware('Illuminate\Session\Middleware\StartSession');
    }
}
