<?php
namespace Idealogica\Lavanda\Field;

use Kris\LaravelFormBuilder\Fields\FormField;

/**
 * Date edition field with popup calendar widget.
 */
class DateType extends FormField
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaults()
    {
        return [
            'type' => 'datetime',
            'format' => 'yy-mm-dd',
            'attr' => ['data-calendar' => '1']];
    }

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::field.date';
    }

    /**
     * {@inheritDoc}
     */
    public function render(
        array $options = [],
        $showLabel = true,
        $showField = true,
        $showError = true)
    {
        $preparedOptions = $this->prepareOptions($options);
        $options['attr']['data-format'] = $preparedOptions['format'];
        return parent::render($options, $showLabel, $showField, $showError);
    }

    /*
    public function setValue($value)
    {
        dd($this->parent);

        dd($this->getModelValueAttribute($this->parent->getModel(), $this->name));

        parent::setValue($value);
        dd(debug_backtrace());
        dd($value);
        dd($this->getValue());
        return $this;
    }
     * 
     */
}
