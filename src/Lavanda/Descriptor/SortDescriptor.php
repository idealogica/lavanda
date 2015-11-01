<?php
namespace Idealogica\Lavanda\Descriptor;

use Form;
use Illuminate\Translation\Translator;

/**
 * Describes which fields can be used for sorting.
 */
class SortDescriptor extends Descriptor
{
    /**
     *
     * @var type
     */
    protected $translator = null;

    /**
     * Laravel translator service instance.
     *
     * @var Translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        parent::__construct();
    }

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
                $items[$key.'#asc'] = $this->translator->trans(
                    'lavanda::common.sort_asc',
                    ['item' => mb_strtolower($item)]);
                $items[$key.'#desc'] = $this->translator->trans(
                    'lavanda::common.sort_desc',
                    ['item' => mb_strtolower($item)]);
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
