<?php
use Lavanda\Field\LookupType;

class LookupTypeTest extends LavandaDBTestCase
{
    public function testFieldOutput()
    {
        $field = new LookupType(
            'name',
            'lookup',
            $this->plainForm,
            [
                'model' => 'App\Post',
                'property' => 'title'
            ]);
        $res = $field->render();
        self::assertCount(
            3,
            $field->getOption('children')['entity']->getOption('children'));
        self::assertEquals(3, substr_count($res, 'type="checkbox"'));
        self::assertEquals(3, substr_count($res, 'name[]'));
    }
}
