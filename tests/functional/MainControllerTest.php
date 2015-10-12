<?php

class MainControllerTest extends LavandaDBTestCase
{
    public function testIndex()
    {
        $this->visit('admin')->
            seePageIs('admin')->
            see('Control panel');
    }
}
