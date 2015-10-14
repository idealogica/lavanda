<?php
namespace Idealogica\Lavanda\Field;

use Kris\LaravelFormBuilder\Fields\ParentType;

/**
 * Represents many-to-many relationship.
 * Allows to associate two objects together.
 * Displays simple checkboxes list that allows to select multiple items.
 */
class LookupType extends ParentType
{
    /**
     * {@inheritDoc}
     */
    protected $valueProperty = 'data';

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::field.lookup';
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaults()
    {
        return [
            'options' => ['is_child' => true],
            'label' => null,
            'data' => null,
            'model' => '',
            'property' => 'name'];
    }

    /**
     * {@inheritDoc}
     */
    protected function createChildren()
    {
        $propery = $this->getOption('property');
        $modelClass = $this->getOption('model');
        $this->children['entity'] = new LookupEntityType(
            $this->name,
            'entity',
            $this->parent,
            [
                'label' => false,
                'expanded' => true,
                'multiple' => true,
                'class' => $modelClass,
                'property' => $propery
            ]);
    }
}
