<?php
namespace Lavanda\Field;

use Kris\LaravelFormBuilder\Fields\FormField;

/**
 * Simple image upload field implementation.
 */
class ImageType extends FormField
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaults()
    {
        return ['clear_text' => 'Clear value'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::field.image';
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
        if($this->getOption($this->valueProperty))
        {
            $options['required'] = false;
            if(isset($this->options['attr']['required']))
            {
                unset($this->options['attr']['required']);
            }
        }
        return parent::render($options, $showLabel, $showField, $showError);
    }
}
