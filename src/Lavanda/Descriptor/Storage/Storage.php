<?php
namespace Idealogica\Lavanda\Descriptor\Storage;

use Illuminate\Http\Request;

/**
 * Describes how to handle external storage.
 */
abstract class Storage
{
    /**
     * Laravel request service instance.
     *
     * @var Request
     */
    protected $request = null;

    /**
     * App base path.
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * App public path.
     *
     * @var string
     */
    protected $publicPath = '';

    /**
     * Name of the storage.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Storage parameters.
     *
     * @var array
     */
    protected $parms = [];

    /**
     * Constructor.
     *
     * @param Request $request
     * @param string $basePath
     * @param string $publicPath
     * @param string $name
     * @param array $parms
     */
    public function __construct(Request $request, $basePath, $publicPath, $name, array $parms = [])
    {
        $this->request = $request;
        $this->basePath = $basePath;
        $this->publicPath = $publicPath;
        $this->name = $name;
        $this->parms = array_merge($this->parms, $parms);
    }

    /**
     * Gets name of storage.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets array of all storage paremeters.
     *
     * @return array
     */
    public function getParms()
    {
        return $this->parms;
    }

    /**
     * Gets specified parameter by name.
     *
     * @param string $name
     * @return mixed
     */
    public function getParm($name)
    {
        return isset($this->parms[$name]) ? $this->parms[$name] : null;
    }

    /**
     * Attaches storage value to passed item.
     *
     * @param Illuminate\Database\Eloquent\Model|array $item Item to attach storage value.
     * @param array $parms Parameters to override.
     */
    public function attachTo(&$item, array $parms = [])
    {
        $this->parms = array_merge($this->parms, $parms);
        $this->attach($item);
    }

    /**
     * Handles storage value loading. Must be overrided in descendant class.
     *
     * @param $item
     */
    abstract public function attach(&$item);

    /**
     * Saves storage value using passed item.
     *
     * @param Illuminate\Database\Eloquent\Model|array $item Item to save with.
     * @param array $parms Parameters to override.
     */
    public function saveWith($item, array $parms = [])
    {
        $this->parms = array_merge($this->parms, $parms);
        $this->save($item);
    }

    /**
     * Handles storage value saving. Must be overrided in descendant class.
     *
     * @param $item
     */
    abstract public function save($item);
}
