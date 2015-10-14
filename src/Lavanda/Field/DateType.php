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
}
