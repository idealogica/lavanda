<?php

class EntityControllerTest extends LavandaDBTestCase
{
    public function testIndex()
    {
        $this->visit('admin/post')->
            seePageIs('admin/post')->
            see('Posts list')->
            see('Search query')->
            see('Search')->
            see('Add new')->
            see('Show')->
            see('Edit')->
            see('Delete')->
            see('admin/post/1')->
            see('admin/post/2')->
            see('admin/post/3')->
            see('admin/post/1/edit')->
            see('admin/post/2/edit')->
            see('admin/post/3/edit')->
            type('_title_', 'query')->
            press('Search')->
            seePageIs('admin/post?query=_title_')->
            see('Posts list')->
            see('_title_')->
            see('admin/post/1')->
            see('admin/post/2')->
            see('admin/post/3')->
            type('qwerty', 'query')->
            press('Search')->
            seePageIs('admin/post?query=qwerty')->
            see('Posts list')->
            dontSee('_title_')->
            dontSee('admin/post/1')->
            dontSee('admin/post/2')->
            dontSee('admin/post/3');
    }

    public function testCreate()
    {
        $this->useForm('admin/post/create')->
            seePageIs('admin/post')->
            see('!test!');
        try
        {
            $this->useForm('admin/post/create', ['created_at' => 'asdas']);
            $this->fail();
        }
        catch (Exception $e) {}
        $this->useForm('admin/post/create', ['title' => ''])->
            seePageIs('admin/post/create')->
            see('alert-danger');
        $this->useForm('admin/post/create', ['body' => '123'])->
            seePageIs('admin/post/create')->
            see('alert-danger');
        $this->useForm('admin/post/create', ['image' => ''])->
            seePageIs('admin/post/create')->
            see('alert-danger');
        $this->rollback();
    }

    public function testShow()
    {
        $this->visit('admin/post/1')->
            seePageIs('admin/post/1')->
            see('Post #1')->
            see('Edit')->
            see('Delete');
    }

    public function testEdit()
    {
        $this->useForm('admin/post/1/edit')->
            seePageIs('admin/post')->
            see('!test!');
        try
        {
            $this->useForm('admin/post/1/edit', ['created_at' => 'asdas']);
            $this->fail();
        }
        catch (Exception $e) {}
        $this->useForm('admin/post/1/edit', ['title' => ''])->
            seePageIs('admin/post/1/edit')->
            see('alert-danger');
        $this->useForm('admin/post/1/edit', ['body' => '123'])->
            seePageIs('admin/post/1/edit')->
            see('alert-danger');
        $this->useForm('admin/post/1/edit', ['image' => ''])->
            seePageIs('admin/post/1/edit')->
            see('alert-danger');
        $this->rollback();
    }

    public function testDestroy()
    {
        $this->delete('admin/post/1')->
            seePageIs('admin/post/1');
        $this->delete('admin/post/2')->
            seePageIs('admin/post/2');
        $this->delete('admin/post/3')->
            seePageIs('admin/post/3')->
            dontSee('_title_');
        $this->rollback();
    }

    private function useForm($uri, array $parms = [])
    {
        $defParms = [
            'created_at' => '1999-09-09',
            'title' => '!test!',
            'body' => 'Body text!',
            'image' => __DIR__.DIRECTORY_SEPARATOR.'EntityControllerTest.jpg'];
        $parms = array_merge($defParms, $parms);
        return $this->visit($uri)->
            seePageIs($uri)->
            type($parms['created_at'], 'created_at')->
            type($parms['title'], 'title')->
            type($parms['body'], 'body')->
            attach($parms['image'], 'image')->
            press('Save');
    }
}
