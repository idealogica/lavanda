<?php
namespace Lavanda;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelController;

/**
 * Base controller of Lavanda's entities.
 */
class Controller extends LaravelController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Model name to use.
     *
     * @var string
     */
    protected $model = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
        if(!($this instanceof EntityController) && !$this->model)
        {
            $className = class_basename(get_called_class());
            $this->model = strtolower(
                preg_replace('/controller$/i', '', $className));
        }
    }

    /**
     * Gets model name.
     *
     * @return string
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Sets model name. If model doesn't exists shows 404 page.
     *
     * @param string $model
     * @return $this
     */
    protected function setModel($model)
    {
        $this->model = $model;
        if(!class_exists($this->getModelClass()))
        {
            abort(404);
        }
        return $this;
    }

    /**
     * Gets route according to model name.
     *
     * @param string $action
     * @param array $parms
     * @return string
     */
    protected function getRoute($action = '', $parms = [])
    {
        return adminRoute($this->model, $action, $parms);
    }

    /**
     * Checks if given action name is allowed by actions descriptor.
     *
     * @param string $action
     * @return bool
     */
    protected function isActionAllowed($action)
    {
        return (bool)$this->staticModelGetActionsDescriptor()->
            getDescription($action);
    }

    /**
     * Checks if given action name is allowed by actions descriptor.
     * If it isn't shows 404 error page.
     *
     * @param string $action
     * @return bool
     */
    protected function checkAction($action)
    {
        $res = $this->isActionAllowed($action);
        if(!$res)
        {
            abort(404);
        }
        return $res;
    }

    /**
     * Gets full namespaced model class name.
     *
     * @return string
     */
    private function getModelClass()
    {
        return getModelClass($this->model);
    }

    /**
     * Implements convenient method of calling static methods from
     * controller's model.
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if(preg_match('/^staticModel([a-zA-Z0-9]+)$/', $method, $m))
        {
            $modelClass = $this->getModelClass();
            $modelMethod = lcfirst($m[1]);
            return call_user_func_array(array($modelClass, $modelMethod), $args);
        }
        return parent::__call($method, $args);
    }
}
