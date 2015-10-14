<?php
use Idealogica\Lavanda\Field\RowSetType;

class RowSetTypeTest extends LavandaDBTestCase
{
    public function testFieldOutput()
    {
        $field = new RowSetType(
            'name',
            'rowset',
            $this->plainForm,
            [
                'model' => 'App\Comment',
                'data' => App\Comment::all()
            ]);
        $res = $field->render();
        self::assertCount(9, $field->getOption('children'));
        self::assertEquals(9, substr_count($res, 'createRowSetRow'));
    }
}
