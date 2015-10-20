<?php
use org\bovigo\vfs\vfsStream;
use Idealogica\Lavanda\Descriptor\StorageDescriptor;

class StorageDescriptorTest extends LavandaTestCase
{
    private $requestMock = null;
    private $descriptor = null;

    public function setUp()
    {
        $this->requestMock = $this->getMockBuilder('Illuminate\Http\Request')->
            disableOriginalConstructor()->
            getMock();
        $this->descriptor = new StorageDescriptor($this->requestMock, '', '');
        $this->descriptor->add('name', 'image', ['path' => 'path']);
    }

    public function testStorageName()
    {
        self::assertEquals('name', $this->descriptor['name']->getName());
    }

    public function testStorageParm()
    {
        self::assertEquals('path', $this->descriptor['name']->getParm('path'));
    }

    public function testStorages()
    {
        $img = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."StorageDescriptorTest.jpg");
        $file = $this->getMockBuilder('Symfony\Component\HttpFoundation\File\UploadedFile')->
            disableOriginalConstructor()->
            getMock();
        $file->expects($this->once())->
            method('isValid')->
            willReturn(true);
        $file->expects($this->once())->
            method('move')->
            with(self::equalTo('vfs://root'), self::equalTo(1));
        $this->requestMock->expects($this->once())->
            method('file')->
            willReturn($file)->
            with(self::equalTo('name'));
        $descriptor = new StorageDescriptor($this->requestMock, '', '');
        $descriptor->add('name', 'image', ['path' => vfsStream::url('root')]);
        // attachTo
        $root = vfsStream::setup();
        vfsStream::newFile('1.jpg')->
            at($root)->
            setContent($img);
        $item = ['id' => 1, 'test' => 'test'];
        $descriptor['name']->attachTo($item);
        self::assertEquals([
            'id' => 1,
            'test' => 'test',
            'name' => 'vfs://root/1.jpg'], $item);
        // saveWith
        $root = vfsStream::setup();
        vfsStream::newFile('1')->
            at($root)->
            setContent($img);
        $item = ['id' => 1, 'test' => 'test'];
        $descriptor['name']->saveWith($item);
        self::assertTrue($root->hasChild('1.jpg'));
    }
}
