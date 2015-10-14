<?php
namespace Idealogica\Lavanda\Field;

use Kris\LaravelFormBuilder\Fields\ParentType;
use Kris\LaravelFormBuilder\Fields\ChildFormType;

/**
 * Represents many-to-one, one-to-one relationships.
 * It allows to edit linked object inline in sub-form.
 */
class FieldSetType extends ParentType
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
        return 'lavanda::field.fieldset';
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
            'model' => ''];
    }

    /**
     * {@inheritDoc}
     */
    protected function createChildren()
    {
        $modelClass = $this->getOption('model');
        $form = $modelClass::createForm(null, null, $this->name);
        // laravel-from-builder hack to fix error borders on all fields with the same name
        fixFormBuilderForm(
            $form,
            $this->formHelper->getConfig('defaults.wrapper_error_class'));
        $this->children['form'] = new ChildFormType($this->name, 'form', $this->parent, [
            'label' => false,
            'class' => $form]);
    }
}
