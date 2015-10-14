<?php
namespace Idealogica\Lavanda\Field;

use Kris\LaravelFormBuilder\Fields\EntityType;

/**
 * Child entity presentation to use in LookupType field.
 */
class LookupEntityType extends EntityType
{
    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::field.lookup_entity';
    }

    /**
     * {@inheritDoc}
     */
    protected function setupValue()
    {
        $value = $this->getOption($this->valueProperty);
        $isChild = $this->getOption('is_child');
        if($value instanceof \Closure)
        {
            $this->valueClosure = $value;
        }
        if(($value === null || $value instanceof \Closure) && !$isChild)
        {
            $val = $this->getModelValueAttribute($this->parent->getModel(), $this->name);
            $this->setValue($val ? array_pluck($val, 'id') : null);
        }
        elseif(!$isChild)
        {
            $this->hasDefault = true;
        }
    }
}
