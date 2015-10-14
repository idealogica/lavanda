<?php
namespace Idealogica\Lavanda\Descriptor;

use Form;

/**
 * Describes which fields can be used for sorting.
 */
class SortDescriptor extends Descriptor
{
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
                $items[$key.'#asc'] = '&#11016; sort by '.mb_strtolower($item);
                $items[$key.'#desc'] = '&#11019; sort by '.mb_strtolower($item);
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
