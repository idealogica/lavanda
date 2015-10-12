<?php
namespace Lavanda\Descriptor;

use Closure;
use Exception;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * Describes how to show various data types in views.
 */
class PresentationDescriptor extends Descriptor
{
    /**
     * Laravel view factory.
     *
     * @var ViewFactory
     */
    private $view = null;

    /**
     * User defined query builder closure.
     *
     * @var Closure
     */
    private $queryBuilder = null;

    /**
     * Constructor.
     *
     * @param ViewFactory $view
     */
    public function __construct(ViewFactory $view)
    {
        parent::__construct();
        $this->view = $view;
    }

    /**
     * Adds new presentation description.
     *
     * @param string $name
     * @param string $type
     * @param string $title
     * @param array $parms
     * @return $this
     */
    public function add($name, $type = 'text', $title = '', array $parms = [])
    {
        $this->items[$name] = $this->createPresentation($name, $type, $title, $parms);
        return $this;
    }

    /**
     * Gets user defined query builder closure.
     *
     * @return Closure
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * Adds user defined query builder.
     *
     * @param Closure $queryBuilder
     * @return $this
     */
    public function addQueryBuilder(Closure $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        return $this;
    }

    /**
     * Creates new presentation description instance.
     *
     * @param string $name
     * @param string $type
     * @param string $title
     * @param array $parms
     * @return \Lavanda\Descriptor\Presentation\Presentation
     * @throws Exception
     */
    private function createPresentation($name, $type, $title = '', array $parms = [])
    {
        $title = $title ?: $name;
        $class = 'Lavanda\\Descriptor\\Presentation\\'.ucfirst($type).'Presentation';
        if(!class_exists($class))
        {
            throw new Exception('Unknown presentation "'.$type.'".');
        }
        return new $class($this->view, $title, $parms);
    }
}
