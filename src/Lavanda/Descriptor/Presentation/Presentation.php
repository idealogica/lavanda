<?php
namespace Lavanda\Descriptor\Presentation;

use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * Describes how to render HTML code for various kinds of data.
 */
abstract class Presentation
{
    /**
     * Laravel view factory.
     *
     * @var ViewFactory
     */
    protected $view = null;

    /**
     * Presentation title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Presentation parameters.
     *
     * @var array
     */
    protected $parms = [
        'width' => null
    ];

    /**
     * Constructor.
     *
     * @param ViewFactory $view
     * @param string $title
     * @param array $parms
     */
    public function __construct(ViewFactory $view, $title, array $parms = [])
    {
        $this->view = $view;
        $this->title = $title;
        $this->parms = array_merge($this->parms, $this->getDefaultParms(), $parms);
    }

    /**
     * Gets default presentation parameters.
     *
     * @return array
     */
    protected function getDefaultParms()
    {
        return [];
    }

    /**
     * Gets presentation title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets array of all presentation paremeters.
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
     * Renders value of presentation.
     *
     * @param mixed $value Value of presentation.
     * @param array $parms Parameters to override.
     * @return string
     */
    public function render($value, array $parms = [])
    {
        if(isset($value))
        {
            return $this->view->make($this->getTemplate(), [
                'value' => $value,
                'title' => $this->title,
                'parms' => array_merge($this->parms, $parms)])->render();
        }
        else
        {
            return '-';
        }
    }

    /**
     * Gets used template. Must be overrided in descendant class.
     */
    abstract protected function getTemplate();
}
