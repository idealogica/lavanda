<?php
use App\Comment;
use Lavanda\Field\FieldSetType;

class FieldSetTypeTest extends LavandaTestCase
{
    public function testFieldOutput()
    {
        $field = new FieldSetType('name', 'fieldset', $this->plainForm, [
            'model' => 'App\Comment'
        ]);
        $res = $field->render();
        $form = Comment::createForm('', '', 'test');
        self::assertEquals(
            count($form->getFields()),
            count($field->getOption('children')['form']->getForm()->getFields()));
        self::assertContains('name[created_at]', $res);
        self::assertContains('name[name]', $res);
        self::assertContains('name[email]', $res);
        self::assertContains('name[body]', $res);
    }
}
