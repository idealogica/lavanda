<?php
namespace Lavanda\Descriptor;

use Exception;
use Illuminate\Http\Request;

/**
 * Describes external storages. Typically these are various kinds of files.
 */
class StorageDescriptor extends Descriptor
{
    /**
     * Laravel request service instance.
     *
     * @var Request
     */
    private $request = null;

    /**
     * App base path.
     *
     * @var string
     */
    private $basePath = '';

    /**
     * App public path.
     *
     * @var string
     */
    private $publicPath = '';

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request, $basePath, $publicPath)
    {
        parent::__construct();
        $this->request = $request;
        $this->basePath = $basePath;
        $this->publicPath = $publicPath;
    }

    /**
     * Adds new storage description.
     *
     * @param string $name
     * @param string $type
     * @param array $parms
     * @return $this
     */
    public function add($name, $type = 'image', array $parms = [])
    {
        $this->items[$name] = $this->createStorage($name, $type, $parms);
        return $this;
    }

    /**
     * Creates new storage description instance.
     *
     * @param string $name
     * @param string $type
     * @param array $parms
     * @return \Lavanda\Descriptor\Storage\Storage
     * @throws Exception
     */
    public function createStorage($name, $type, array $parms = [])
    {
        $class = 'Lavanda\\Descriptor\\Storage\\'.ucfirst($type).'Storage';
        if(!class_exists($class))
        {
            throw new Exception('Unknown storage "'.$type.'".');
        }
        return new $class($this->request, $this->basePath, $this->publicPath, $name, $parms);
    }
}
