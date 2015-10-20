<?php
namespace Idealogica\Lavanda\Field;

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
        return [
            'clear_text' => 'Clear value',
            'required' => false];
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
        if(isset($options['attr']) && is_array($options['attr']))
        {
            $options['attr']['class'] = !empty($options['attr']['class']) ?
                $options['attr']['class'].' lavanda-image-upload' :
                $this->options['attr']['class'].' lavanda-image-upload';
        }
        else
        {
            $options['attr'] = [
                'class' => $this->options['attr']['class'].' lavanda-image-upload'];
        }
        if($this->getValue())
        {
            $options['rules'] = str_replace(
                ['required|', '|required', 'required'],
                '',
                $this->getOption('rules'));
            if(isset($this->options['attr']['required']))
            {
                unset($this->options['attr']['required']);
            }
        }
        return parent::render($options, $showLabel, $showField, $showError);
    }
}
