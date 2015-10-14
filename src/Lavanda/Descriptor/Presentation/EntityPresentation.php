<?php
namespace Idealogica\Lavanda\Descriptor\Presentation;

/**
 * Describes how to render HTML code for named by preperty sub-items.
 */
class EntityPresentation extends Presentation
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaultParms()
    {
        return [
        'max_len' => null,
        'model' => '',
        'property' => 'name'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getTemplate()
    {
        return 'lavanda::presentation.entity';
    }
}
