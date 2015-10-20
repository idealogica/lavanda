<?php
namespace Idealogica\Lavanda\Descriptor;

use Illuminate\Support\Collection;

/**
 * Can be used to describe various kinds of things in the same unified way.
 * Examples of using: allowed controller actions, fields to use in search.
 */
class Descriptor extends Collection
{
    /**
     * Adds new description.
     *
     * @param string $name Description name.
     * @param mixed $value Description.
     * @return $this
     */
    public function add($name, $value = true)
    {
        $this->items[$name] = $value;
        return $this;
    }

    /**
     * Gets description.
     *
     * @param string $name Description name.
     * @return mixed
     */
    public function getDescription($name)
    {
        return !empty($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Determines if there is a specified description.
     *
     * @param string $name Description name.
     * @return bool
     */
    public function hasDescription($name)
    {
        return !empty($this->items[$name]);
    }

    /**
     * Deletes specified description.
     *
     * @param string $name
     * @return $this
     */
    public function removeDescription($name)
    {
        if($this->hasDescription($name))
        {
            unset($this->items[$name]);
        }
        return $this;
    }
}
