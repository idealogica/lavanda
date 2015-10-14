<?php
namespace Idealogica\Lavanda\Descriptor\Presentation;

/**
 * Describes how to render HTML code for text data.
 */
class TextPresentation extends Presentation
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaultParms()
    {
        return ['max_len' => null];
    }

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::presentation.text';
    }
}
