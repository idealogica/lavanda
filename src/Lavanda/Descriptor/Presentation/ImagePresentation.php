<?php
namespace Lavanda\Descriptor\Presentation;

/**
 * Describes how to render HTML code for images.
 */
class ImagePresentation extends Presentation
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaultParms()
    {
        return [
        'img_width' => null,
        'img_height' => null,
        'img_attrs' => []];
    }

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::presentation.image';
    }
}
