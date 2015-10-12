<?php
use Lavanda\Descriptor\PresentationDescriptor;

class PresentationDescriptorTest extends LavandaTestCase
{
    private $viewFactory;

    public function setUp()
    {
        $this->viewFactory = $this->getMockBuilder('Illuminate\View\Factory')->
            disableOriginalConstructor()->
            getMock();
    }

    public function testQueryBuilder()
    {
        self::assertEquals('test', (new PresentationDescriptor($this->viewFactory))->
            addQueryBuilder(function () { return 'test'; })->
            getQueryBuilder()->__invoke());
    }

    public function testPresenatations()
    {
        $view = $this->getMockBuilder('Illuminate\View\View')->
            disableOriginalConstructor()->
            getMock();
        $view->expects($this->exactly(3))->method('render');
        $this->viewFactory->expects($this->exactly(3))->method('make')->willReturn($view)->
            withConsecutive(
                [
                    self::equalTo('lavanda::presentation.text'),
                    self::equalTo([
                        'value' => 'value',
                        'title' => 'title',
                        'parms' => [
                            'max_len' => 5,
                            'width' => null]])
                ],
                [
                    self::equalTo('lavanda::presentation.image'),
                    self::equalTo([
                        'value' => 'value',
                        'title' => 'title',
                        'parms' => [
                            'width' => null,
                            'img_width' => 5,
                            'img_height' => null,
                            'img_attrs' => []]])
                ],
                [
                    self::equalTo('lavanda::presentation.entity'),
                    self::equalTo([
                        'value' => 'value',
                        'title' => 'title',
                        'parms' => [
                            'width' => null,
                            'max_len' => null,
                            'model' => 'model',
                            'property' => 'name']])
                ]);
        (new PresentationDescriptor($this->viewFactory))->
            add('name', 'text', 'title', ['max_len' => 5])['name']->
            render('value');
        (new PresentationDescriptor($this->viewFactory))->
            add('name', 'image', 'title', ['img_width' => 5])['name']->
            render('value');
        $p = new PresentationDescriptor($this->viewFactory);
        $p->add('name', 'entity', 'title', ['model' => 'model'])['name']->
            render('value');
        $this->assertEquals('model', $p['name']->getParm('model'));
        $this->assertEquals('title', $p['name']->getTitle());
    }
}
