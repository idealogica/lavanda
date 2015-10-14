<?php
namespace Idealogica\Lavanda\Field;

use Illuminate\Support\Collection;
use Kris\LaravelFormBuilder\Fields\ParentType;
use Kris\LaravelFormBuilder\Fields\ChildFormType;

/**
 * Multiple sub-forms (rows) container with ability to inline edition.
 * It allows to add new rows to form and delete existing.
 * Relation type: one-to-many.
 */
class RowSetType extends ParentType
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
        return 'lavanda::field.rowset';
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaults()
    {
        return [
            'options' => ['is_child' => true],
            'data' => null,
            'prototype' => null,
            'model' => '',
            'row_label' => 'Item',
            'add_text' => 'Add item',
            'remove_text' => 'Remove item',
            'rows_count' => 0];
    }

    /**
     * {@inheritDoc}
     */
    protected function createChildren()
    {
        $this->children = [];
        $data = $this->parent->getRequest()->input($this->getNameKey());
        if(!$data)
        {
            $data = $this->parent->getRequest()->old($this->getNameKey());
        }
        if(!$data)
        {
            $data = $this->getOption($this->valueProperty, []);
        }
        if($data instanceof Collection)
        {
            $data = $data->all();
        }
        $this->setOption('prototype', $this->createChildForm('__IDX__'));
        if(!$data)
        {
            return;
        }
        if(!is_array($data) && !$data instanceof \Traversable)
        {
            throw new \Exception(
                'Data for collection field ['.$this->name.'] must be iterable.');
        }
        foreach($data as $key => $row)
        {
            $this->children[] = $this->createChildForm($key, $row);
        }
        $this->setOption('rows_count', count($this->children));
    }

    /**
     * Creates new instance of child form.
     *
     * @param int|string $idx
     * @param \Illuminate\Database\Eloquent\Model|array $model
     * @return ChildFormType
     */
    private function createChildForm($idx, $model = null)
    {
        $name = $this->name.'['.$idx.']';
        $modelClass = $this->getOption('model');
        $form = $modelClass::createForm(null, null, $name, $model);
        // laravel-from-builder hack to fix error borders on all fields with same name
        fixFormBuilderForm(
            $form,
            $this->formHelper->getConfig('defaults.wrapper_error_class'));
        return new ChildFormType($name, 'form', $this->parent, [
            'label' => $this->getOption('row_label').' #'.(is_string($idx) ? $idx : ++$idx),
            'class' => $form]);
    }
}
