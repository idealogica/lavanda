<?php
namespace Lavanda;

use Color;
use Validator;
use Colors\RandomColor;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Config\Repository;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Kris\LaravelFormBuilder\FormHelper;
use Kris\LaravelFormBuilder\FormBuilder;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\Validation\Factory as ValidationFactory;

/**
 * Service provider for Lavanda package.
 */
class LavandaServiceProvider extends ServiceProvider
{
    /**
     * Lavanda's engine initialisation method.
     *
     * @param Request $request
     * @param Router $router
     * @param ViewFactory $viewFactory
     * @param ValidationFactory $validationFactory
     * @param Filesystem $fileSystem
     * @param FormBuilder $formBuilder
     */
    public function boot(
        Repository $config,
        Request $request,
        Router $router,
        ViewFactory $viewFactory,
        ValidationFactory $validationFactory,
        Filesystem $fileSystem,
        FormBuilder $formBuilder)
    {
        // model dependencies
        Model::setRequest($request);
        Model::setView($viewFactory);
        Model::setFormBuilder($formBuilder);
        Model::setBasePath($this->app->basePath());
        Model::setPublicPath($this->app->make('path.public'));

        // adding Lavanda controllers to routing
        if(!$this->app->routesAreCached())
        {
            $router->get('admin', [
                'uses' => 'Lavanda\MainController@index',
                'as' => 'admin.main.index']);
            $router->get('admin/_image', [
                'uses' => 'Lavanda\ImageController@index',
                'as' => 'admin.image.index']);
            $router->get('admin/{model}', [
                'uses' => 'Lavanda\EntityController@index',
                'as' => 'admin.entity.index']);
            $router->get('admin/{model}/create', [
                'uses' => 'Lavanda\EntityController@create',
                'as' => 'admin.entity.create']);
            $router->post('admin/{model}', [
                'uses' => 'Lavanda\EntityController@store',
                'as' => 'admin.entity.store']);
            $router->get('admin/{model}/{id}', [
                'uses' => 'Lavanda\EntityController@show',
                'as' => 'admin.entity.show']);
            $router->get('admin/{model}/{id}/edit', [
                'uses' => 'Lavanda\EntityController@edit',
                'as' => 'admin.entity.edit']);
            $router->put('admin/{model}/{id}', [
                'uses' => 'Lavanda\EntityController@update',
                'as' => 'admin.entity.update']);
            $router->delete('admin/{model}/{id}', [
                'uses' => 'Lavanda\EntityController@destroy',
                'as' => 'admin.entity.destroy']);
        }

        // custom image validation method
        $validationFactory->extend(
            'lavanda_image',
            function($attribute, $value, $parameters) use ($request)
            {
                if($request->hasFile($attribute))
                {
                    $validator = Validator::make($request->all(), [
                        $attribute => 'min:1|max:10000|mimes:'.implode(',', $parameters)]);
                    return !$validator->fails();
                }
                return true;
            });

        // view composer
        $viewFactory->composer(
            ['lavanda::layout.common', 'lavanda::main.index'],
            function (View $view) use ($config, $fileSystem, $request)
            {
                $menu = [];
                $maxCount = 0;
                $modelPath = $config->get('lavanda.model_path');
                $files = array_values($fileSystem->files($modelPath));
                mt_srand(10);
                $colors = RandomColor::many(count($files), [
                    'luminosity' => 'light',
                    'hue' => 265]);
                foreach($files as $idx => $file)
                {
                    $model = strtolower(basename($file, ".php"));
                    $modelClass = getModelClass($model);
                    if(!in_array('Lavanda\Model', class_parents($modelClass)))
                    {
                        continue;
                    }
                    $url = adminRoute($model, 'index');
                    $count = $modelClass::count();
                    $maxCount = $count > $maxCount ? $count : $maxCount;
                    $menu[] = [
                        'title'    => $modelClass::getPluralName(),
                        'url'      => $url,
                        'selected' => preg_match(
                            '/^'.preg_quote($url, '/').'/',
                            $request->url()),
                        'count'    => $count,
                        'columns'  => 1,
                        'color'    => (string)(new Color($colors[$idx]))->
                            desaturate(50)->
                            lighten(5)];
                }
                $tiles = $menu;
                if($maxCount)
                {
                    $rowItems = [];
                    $countRow = function ($rowItems)
                    {
                        $rowColumns = 0;
                        foreach($rowItems as $item)
                        {
                            $rowColumns += $item['columns'];
                        }
                        return $rowColumns;
                    };
                    $stretchRow = function(array &$rowItems) use ($countRow)
                    {
                        $count = $countRow($rowItems);
                        if($count < 12)
                        {
                            $add = (int)((12 - $count)/count($rowItems));
                            foreach($rowItems as &$rowItem)
                            {
                                $rowItem['columns'] += $add;
                            }
                            foreach($rowItems as &$rowItem)
                            {
                                if($countRow($rowItems) >= 12)
                                {
                                    break;
                                }
                                $rowItem['columns']++;
                            }
                        }
                    };
                    usort($tiles, function ($a, $b)
                    {
                        if($a['count'] == $b['count'])
                        {
                            return 0;
                        }
                        return ($a['count'] > $b['count']) ? -1 : 1;
                    });
                    foreach($tiles as &$item)
                    {
                        $columns = (int)round(12 * $item['count'] / $maxCount);
                        $item['columns'] = $columns ?: 1;
                        $rowItems[] = &$item;
                        $rowColumns = $countRow($rowItems);
                        if($rowColumns > 12)
                        {
                            unset($rowItems[count($rowItems) - 1]);
                            $stretchRow($rowItems);
                            $rowItems = [];
                            $rowItems[] = &$item;
                        }
                        elseif($rowColumns == 12)
                        {
                            $rowItems = [];
                        }
                    }
                    $stretchRow($rowItems);
                }
                $view->with('menu', $menu)->with('tiles', $tiles);
            });

        // request rebinding
        $this->app->rebinding('request', function ($app, $request)
        {
            $app->forgetInstance('laravel-form-helper');
            $app->forgetInstance('laravel-form-builder');
            $app->bindShared('laravel-form-helper', function ($app) use ($request)
            {
                $configuration = $app['config']->get('laravel-form-builder');
                return new FormHelper($app['view'], $request, $configuration);
            });
            $app->bindShared('laravel-form-builder', function ($app)
            {
                return new FormBuilder($app, $app['laravel-form-helper']);
            });
            Model::flush();
            Model::setRequest($request);
            Model::setFormBuilder($app->make('laravel-form-builder'));
        });

        // package configuration
        $packageDir = dirname(__DIR__);
        $viewsPath = $packageDir.'/views';
        $translationsPath = $packageDir.'/lang';
        $assetsPath = $packageDir.'/assets';
        $configPath = $packageDir.'/config/common.php';
        $this->loadViewsFrom($viewsPath, 'lavanda');
        $this->loadTranslationsFrom($translationsPath, 'lavanda');
        $this->mergeConfigFrom($configPath, 'lavanda');
        $this->publishes(
            [$translationsPath => base_path('resources/lang/vendor/lavanda')],
            'lang');
        $this->publishes(
            [$viewsPath => base_path('resources/views/vendor/lavanda')],
            'views');
        $this->publishes(
            [$assetsPath => public_path('vendor/lavanda')],
            'public');
        $this->publishes(
            [$configPath => config_path('lavanda.php')],
            'config');
    }

    /**
     * Sets config for Form Builder's custom fields.
     */
    public function register()
    {
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.date',
            'Lavanda\\Field\DateType');
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.image',
            'Lavanda\\Field\ImageType');
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.fieldset',
            'Lavanda\\Field\\FieldSetType');
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.rowset',
            'Lavanda\\Field\\RowSetType');
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.lookup',
            'Lavanda\\Field\LookupType');
        $this->app['config']->set(
            'laravel-form-builder.custom_fields.lookup_entity',
            'Lavanda\\Field\LookupEntityType');
    }
}
