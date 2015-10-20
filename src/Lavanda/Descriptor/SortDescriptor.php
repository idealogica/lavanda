<?php
namespace Idealogica\Lavanda\Descriptor;

use Form;

/**
 * Describes which fields can be used for sorting.
 */
class SortDescriptor extends Descriptor
{
    /**
     * {@inheritDoc}
     */
    public function add($name, $title = '')
    {
        return parent::add($name, $title ?: $name);
    }

    /**
     * Renders sort select.
     *
     * @return string
     */
    public function renderSortSelect()
    {
        if($this->items)
        {
            $items = [];
            foreach($this->items as $key => $item)
            {
                $items[$key.'#asc'] = 'sort by '.mb_strtolower($item).' asc';
                $items[$key.'#desc'] = 'sort by '.mb_strtolower($item).' desc';
            }
            $value = getUnencryptedCookie('sort');
            return Form::select(
                'sort',
                $items,
                $value ?: 'id#desc',
                ['class' => 'form-control', 'id' => 'sort']);
        }
    }
}
