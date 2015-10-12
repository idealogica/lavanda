<?php
use Lavanda\Field\ImageType;

class ImageTypeTest extends LavandaTestCase
{
    public function testFieldOutput()
    {
        $field = new ImageType(
            'name',
            'image',
            $this->plainForm,
            ['clear_text' => '_clear_text_', 'value' => 'img.jpg']);
        $res = $field->render();
        self::assertContains('_clear_text_', $res);
        self::assertContains('img.jpg', $res);
        self::assertContains('value="img.jpg"', $res);
        self::assertContains('type="hidden"', $res);
    }
}
