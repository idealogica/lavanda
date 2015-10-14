<?php
use Idealogica\Lavanda\Descriptor\Descriptor;

class DescriptorTest extends LavandaTestCase
{
    public function testAddGetAndHas()
    {
        $d = (new Descriptor)->add('test', 'test');
        self::assertEquals('test', $d['test']);
        self::assertEquals('test', $d->getDescription('test'));
        self::assertTrue($d->hasDescription('test'));
    }
}
