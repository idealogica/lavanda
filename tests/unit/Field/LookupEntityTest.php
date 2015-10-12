<?php
use Lavanda\Field\LookupEntityType;

class LookupEntityTypeTest extends LavandaTestCase
{
    public function testFieldOutput()
    {
        $field = new LookupEntityType(
            'name',
            'lookup_entity',
            $this->plainForm,
            [
                'class' => 'App\\Post',
                'query_builder' => function ($m)
                {
                    return [
                        0 => ['id' => '1', 'attr' => 'attr1'],
                        1 => ['id' => '2', 'attr' => 'attr2']];
                }
            ]);
        $res = $field->render();
        self::assertContains('<option value="id">1</option>', $res);
        self::assertContains('<option value="id">2</option>', $res);
    }
}
