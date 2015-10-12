<?php
use org\bovigo\vfs\vfsStream;

class ImageControllerTest extends LavandaDBTestCase
{
    public function testIndex()
    {
        $img = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."ImageControllerTest.jpg");
        $root = vfsStream::setup();
        vfsStream::newFile('1.jpg')->
            at($root)->
            setContent($img);
        $this->app['config']->set('lavanda.image_cache_path', vfsStream::url('root'));
        $this->visit('admin/_image?src='.vfsStream::url('root').'/1.jpg&width=100')->
            seeHeader('Content-Type', 'image/jpeg')->
            see($img);
    }
}
