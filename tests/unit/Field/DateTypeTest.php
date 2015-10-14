<?php
use Idealogica\Lavanda\Field\DateType;

class DateTypeTest extends LavandaTestCase
{
    public function testFieldOutput()
    {
        $field = new DateType('name', 'date', $this->plainForm);
        $res = $field->render();
        self::assertContains('input', $res);
        self::assertContains('data-calendar="1"', $res);
        self::assertContains('data-format="yy-mm-dd"', $res);
    }
}
