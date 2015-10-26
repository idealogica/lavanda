<?php
namespace Idealogica\Lavanda;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilder;
use Idealogica\Lavanda\Descriptor\Descriptor;
use Idealogica\Lavanda\Descriptor\SortDescriptor;
use Idealogica\Lavanda\Descriptor\StorageDescriptor;
use Idealogica\Lavanda\Descriptor\PresentationDescriptor;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Base class for any Lavanda model.
 * Inherit from it if you want to use your model in control panel interface.
 */
abstract class Model extends EloquentModel
{
    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * Laravel request service instance.
     *
     * @var Request
     */
    protected static $request = null;

    /**
     * Laravel view factory.
     *
     * @var ViewFactory
     */
    protected static $view = null;

    /**
     * Form Builder instance.
     *
     * @var FormBuilder
     */
    protected static $formBuilder = null;

    /**
     * App base path.
     *
     * @var string
     */
    protected static $basePath = '';

    /**
     * App public path.
     *
     * @var string
     */
    protected static $publicPath = '';

    /**
     * Array of instances of controller actions descriptor.
     *
     * @var array
     */
    protected static $actionsDescriptors = [];

    /**
     * Array of instances of external storages descriptor.
     *
     * @var array
     */
    protected static $storageDescriptors = [];

    /**
     * Array of instances of list fields descriptor.
     *
     * @var array
     */
    protected static $listDescriptors = [];

    /**
     * Array of instances of item fields descriptor.
     *
     * @var array
     */
    protected static $itemDescriptors = [];

    /**
     * Array of search descriptors instances.
     *
     * @var array
     */
    protected static $searchDescriptors = [];

    /**
     * Array of sort descriptors instances.
     *
     * @var array
     */
    protected static $sortDescriptors = [];

    /**
     * Array of delete descriptors instances.
     *
     * @var array
     */
    protected static $deleteDescriptors = [];

    /**
     * Array of models input forms.
     *
     * @var array
     */
    protected static $forms = [];

    /**
     * Array of models search forms.
     *
     * @var array
     */
    protected static $searchForms = [];

    /**
     * Flushes model cache data.
     */
    public static function flush()
    {
        self::$actionsDescriptors = [];
        self::$storageDescriptors = [];
        self::$listDescriptors = [];
        self::$itemDescriptors = [];
        self::$searchDescriptors = [];
        self::$sortDescriptors = [];
        self::$forms = [];
        self::$searchForms = [];
    }

    /**
     * Sets Laravel request service instance.
     *
     * @param Request $request
     */
    public static function setRequest(Request $request)
    {
        self::$request = $request;
    }

    /**
     * Sets Laravel view factory instance.
     *
     * @param ViewFactory $view
     */
    public static function setView(ViewFactory $view)
    {
        self::$view = $view;
    }

    /**
     * Sets Form Builder instance.
     *
     * @param FormBuilder $formBuilder
     */
    public static function setFormBuilder(FormBuilder $formBuilder)
    {
        self::$formBuilder = $formBuilder;
    }

    /**
     * Sets app base path.
     *
     * @param string $basePath
     */
    public static function setBasePath($basePath)
    {
        self::$basePath = $basePath;
    }

    /**
     * Sets app public path.
     *
     * @param string $publicPath
     */
    public static function setPublicPath($publicPath)
    {
        self::$publicPath = $publicPath;
    }

    /**
     * Gets model name.
     *
     * @return string
     */
    public static function getName()
    {
        return class_basename(get_called_class());
    }

    /**
     * Gets plural model name.
     *
     * @return string
     */
    public static function getPluralName()
    {
        return str_plural(static::getName());
    }

    /**
     * Determines if model has a custom controller.
     *
     * @return boolean
     */
    public static function hasController()
    {
        return false;
    }

    /**
     * Gets number of list items per page.
     *
     * @return int
     */
    public static function getItemsPerPage()
    {
        return 20;
    }

    /**
     * Gets previously defined descriptor of controller actions.
     *
     * @return Descriptor
     */
    public static function getActionsDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$actionsDescriptors[$class]))
        {
            self::$actionsDescriptors[$class] = new Descriptor;
            static::buildActionsDescriptor(self::$actionsDescriptors[$class]);
        }
        return self::$actionsDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of external storages.
     *
     * @return StorageDescriptor
     */
    public static function getStorageDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$storageDescriptors[$class]))
        {
            self::$storageDescriptors[$class] = new StorageDescriptor(
                self::$request,
                self::$basePath,
                self::$publicPath);
            static::buildStorageDescriptor(self::$storageDescriptors[$class]);
        }
        return self::$storageDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of fields for list of items.
     *
     * @return PresentationDescriptor
     */
    public static function getListDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$listDescriptors[$class]))
        {
            self::$listDescriptors[$class] = new PresentationDescriptor(self::$view);
            static::buildListDescriptor(self::$listDescriptors[$class]);
        }
        return self::$listDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of fields for item page.
     *
     * @return PresentationDescriptor
     */
    public static function getItemDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$itemDescriptors[$class]))
        {
            self::$itemDescriptors[$class] = new PresentationDescriptor(self::$view);
            static::buildItemDescriptor(self::$itemDescriptors[$class]);
        }
        return self::$itemDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of fields for searching.
     *
     * @return Descriptor
     */
    public static function getSearchDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$searchDescriptors[$class]))
        {
            self::$searchDescriptors[$class] = new Descriptor;
            static::buildSearchDescriptor(self::$searchDescriptors[$class]);
        }
        return self::$searchDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of fields for sorting.
     *
     * @return SortDescriptor
     */
    public static function getSortDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$sortDescriptors[$class]))
        {
            self::$sortDescriptors[$class] = new SortDescriptor;
            static::buildSortDescriptor(self::$sortDescriptors[$class]);
        }
        return self::$sortDescriptors[$class];
    }

    /**
     * Gets previously defined descriptor of relations to delete.
     *
     * @return Descriptor
     */
    public static function getDeleteDescriptor()
    {
        $class = get_called_class();
        if(empty(self::$deleteDescriptors[$class]))
        {
            self::$deleteDescriptors[$class] = new Descriptor;
            static::buildDeleteDescriptor(self::$deleteDescriptors[$class]);
        }
        return self::$deleteDescriptors[$class];
    }

    /**
     * Creates new model record instance.
     *
     * @param array $attributes Record attributes
     * @return static
     */
    public static function createInstance(array $attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Creates new instance of user input form.
     *
     * @param string $method From method
     * @param string $url Form action URL
     * @param string $config Indicates if it's a child form.
     * @param EloquentModel|array $model Default values for fields.
     * @return Form
     */
    public static function createForm(
        $method = null,
        $url = null,
        $config = null,
        $model = null)
    {
        $form = self::$formBuilder->plain([
            'method' => $method,
            'url' => $url,
            'model' => $model,
            'files' => true]);
        static::buildForm($form, $config);
        return $form;
    }

    /**
     * Gets existing instance or create a new user input form.
     *
     * @param string $method From method
     * @param string $url Form action URL
     * @param EloquentModel|array $model Default values for fields.
     * @return Form
     */
    public static function getForm($method = null, $url = null, $model = null)
    {
        $class = get_called_class();
        if(empty(self::$forms[$class]))
        {
            self::$forms[$class] = self::createForm($method, $url, null, $model);
            self::$forms[$class]->add(
                'save',
                'submit',
                ['label' => trans('lavanda::common.save_button')]);
        }
        return self::$forms[$class];
    }

    /**
     * Gets existing instance or create a new search input form.
     *
     * @param string $action Form action URL
     * @return Form
     */
    public static function getSearchForm($action = '')
    {
        $class = get_called_class();
        if(empty(self::$searchForms[$class]))
        {
            self::$searchForms[$class] = self::$formBuilder->
                plain([
                    'class' => 'form-inline',
                    'method' => 'get',
                    'url' => $action])->
                add('query', 'text', [
                    'label' => false,
                    'attr' => [
                        'placeholder' =>
                            trans('lavanda::common.search_placeholder')]])->
                add('search', 'submit', [
                    'label' => trans('lavanda::common.search_button')])->
                add('reset', 'button', [
                    'label' => trans('lavanda::common.search_reset'),
                    'attr' => [
                        'id' => 'btn-reset',
                        'data-url' => $action]]);
        }
        return self::$searchForms[$class];
    }

    /**
     * Gets items for list.
     *
     * @param string $searchQuery
     * @param string $sort
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getList($searchQuery = '', $sort = '')
    {
        $model = new static;
        $query = $model->newQuery();
        $queryBuilder = self::getListDescriptor()->getQueryBuilder();
        if($queryBuilder)
        {
            $queryBuilder($query);
        }
        $searchBy = self::getSearchDescriptor();
        if(count($searchBy) && $searchQuery)
        {
            $query->where($query->getQuery()->raw('1 = 1'));
            $keywords = explode(' ', trim($searchQuery));
            foreach($searchBy as $field => $search)
            {
                $query->orWhere(function ($query) use ($field, $keywords)
                {
                    foreach($keywords as $keyword)
                    {
                        $query->where($field, 'like', '%'.trim($keyword).'%');
                    }
                });
            }
            self::getSearchForm()->
                getField('query')->
                setOption('value', $searchQuery);
        }
        $sortBy = $model->getTable().'.id';
        $sortDirection = '';
        if($sort)
        {
            list($sortBy, $sortDirection) = explode('#', $sort);
        }
        $query->orderBy($sortBy, $sortDirection);
        $items = $query->paginate(static::getItemsPerPage());
        foreach($items as $item)
        {
            self::attachStorages($item);
        }
        return $items;
    }

    /**
     * Gets single item for show action.
     *
     * @param int $id
     * @return static
     */
    public static function getItem($id)
    {
        $query = (new static)->newQuery()->where('id', $id);
        $itemDescriptor = self::getItemDescriptor();
        $queryBuilder = $itemDescriptor->getQueryBuilder();
        if($queryBuilder)
        {
            $queryBuilder($query);
        }
        return self::attachStorages($query->first());
    }

    /**
     * Gets single record that fills form.
     *
     * @param int $id
     * @return static
     */
    public static function getFormItem($id)
    {
        $query = (new static)->newQuery()->where('id', $id);
        static::buildFormQuery($query);
        return self::attachStorages($query->first());
    }

    /**
     * Attaches external storages to a given record.
     *
     * @param EloquentModel $item
     * @return EloquentModel
     */
    private static function attachStorages(Model $item = null)
    {
        if(!empty($item['id']))
        {
            foreach($item->getRelations() as $value)
            {
                if($value instanceof Model)
                {
                    self::attachStorages($value);
                }
                elseif($value instanceof Collection)
                {
                    foreach($value as $subItem)
                    {
                        self::attachStorages($subItem);
                    }
                }
            }
            $class = get_class($item);
            $storages = $class::getStorageDescriptor();
            foreach($storages as $storage)
            {
                $storage->attachTo($item);
            }
        }
        return $item;
    }

    /**
     * If overridden can be used for adjustment of controller actions descriptor.
     *
     * @param Descriptor $descriptor
     */
    public static function buildActionsDescriptor(Descriptor $descriptor) {}

    /**
     * If overridden can be used for adjustment of external storages descriptor.
     *
     * @param StorageDescriptor $descriptor
     */
    public static function buildStorageDescriptor(StorageDescriptor $descriptor) {}

    /**
     * Used for adjustment of items list descriptor.
     *
     * @param PresentationDescriptor $descriptor
     * @throws BadMethodCallException
     */
    public static function buildListDescriptor(PresentationDescriptor $descriptor)
    {
        throw new BadMethodCallException(
            'Method buildListDescriptor must be implemented in descendant class.');
    }

    /**
     * Used for adjustment of item info page descriptor.
     *
     * @param PresentationDescriptor $descriptor
     * @throws BadMethodCallException
     */
    public static function buildItemDescriptor(PresentationDescriptor $descriptor)
    {
        throw new BadMethodCallException(
            'Method buildItemDescriptor must be implemented in descendant class.');
    }

    /**
     * If overridden can be used for adjustment of search descriptor.
     *
     * @param PresentationDescriptor $descriptor
     */
    public static function buildSearchDescriptor(Descriptor $descriptor) {}

    /**
     * If overridden can be used for adjustment of sort descriptor.
     *
     * @param SortDescriptor $descriptor
     */
    public static function buildSortDescriptor(SortDescriptor $descriptor) {}

    /**
     * If overridden can be used for adjustment of delete descriptor.
     *
     * @param Descriptor $descriptor
     */
    public static function buildDeleteDescriptor(Descriptor $descriptor) {}

    /**
     * If overridden can be used for adjustment of form item query.
     *
     * @param Builder $query
     */
    public static function buildFormQuery(Builder $query) {}

    /**
     * If overridden can be used for adjustment of item input form.
     *
     * @param Form $form
     * @param string $config
     */
    public static function buildForm(Form $form, $config) {}

    /**
     * Accessor that converts created_at model attribute to specified format.
     *
     * @param string $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(trans('lavanda::common.date_format'));
    }

    /**
     * Mutator that converts created_at model attribute to specified format.
     *
     * @param string $value
     */
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->
            format($this->getDateFormat());
    }

    /**
     * Accessor that converts updated_at model attribute to specified format.
     *
     * @param string $value
     * @return string
     */
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format(trans('lavanda::common.date_format'));
    }

    /**
     * Mutator that converts updated_at model attribute to specified format.
     *
     * @param string $value
     */
    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = Carbon::parse($value)->
            format($this->getDateFormat());
    }

    /**
     * Saves record with any sub-collections including sub-collections with
     * one-to-one, one-to-many, many-to-many relationships.
     *
     * @param array $value Value of item and sub-items.
     * @param string $prefix Parent items names chained.
     * @return $this
     */
    public function saveWithRelations(array $value, $prefix = '')
    {
        $relationValues = [];
        $storages = static::getStorageDescriptor();
        foreach($value as $k => $v)
        {
            if(is_array($v))
            {
                $relationValues[$k] = $v;
                unset($value[$k]);
            }
            if($storages->getDescription($k))
            {
                unset($value[$k]);
            }
        }
        $this->fill($value)->save();
        foreach($relationValues as $k => $v)
        {
            $relation = $this->$k();
            if($relation instanceof HasOneOrMany)
            {
                // save one-to-many relationship collection
                $relation->delete();
                foreach($v as $idx => $row)
                {
                    $instance = $relation->getRelated()->newInstance();
                    $instance->setAttribute(
                        $relation->getPlainForeignKey(),
                        $this->id);
                    $instance->saveWithRelations($row, ($prefix ? $prefix.'.' : '').$k.'.'.$idx);
                }
            }
            elseif($relation instanceof BelongsTo)
            {
                // save one-to-one relationship collection
                $instance = $relation->getResults();
                if(!$instance)
                {
                    $instance = $relation->getRelated()->newInstance();
                }
                $instance->saveWithRelations($v, ($prefix ? $prefix.'.' : '').$k);
                $this->setAttribute(
                    $relation->getForeignKey(),
                    $instance['id']);
                $this->save();
            }
            elseif($relation instanceof BelongsToMany)
            {
                // save many-to-many relationship collection
                $relation->sync($v);
            }
        }
        foreach($storages as $storage)
        {
            $storage->saveWith($this, $prefix);
        }
        return $this;
    }

    public function deleteWithRelations()
    {
        foreach(static::getDeleteDescriptor() as $relName => $d)
        {
            $relation = $this->$relName();
            if($relation instanceof HasOneOrMany)
            {
                // delete one-to-many relationship collection
                foreach($relation->getResults() as $item)
                {
                    $item->deleteWithRelations();
                }
            }
            elseif($relation instanceof BelongsTo)
            {
                // delete one-to-one relationship collection
                $relation->getResults()->deleteWithRelations();
            }
            elseif($relation instanceof BelongsToMany)
            {
                // delete many-to-many relationship collection
                $relation->detach();
            }
        }
        foreach(static::getStorageDescriptor() as $storage)
        {
            $storage->deleteWith($this);
        }
        $this->delete();
        return $this;
    }
}
