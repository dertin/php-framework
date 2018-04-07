<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers Particle\Apps\testingController
 */
final class testingControllerTest extends TestCase
{
    protected $_view;

    public function setup()
    {
        $this->_view = new \Smarty();
    }

    public function testBootstrapDefault()
    {
      try {

        Particle\Core\Bootstrap::run(new Particle\Core\Request('testing/testing'));
        $this->assertTrue(true);

      } catch (\Exception $e) {
        $this->fail('Exception: '.$e->getMessage());
      }

    }

    public function testSumar()
    {
      
      $cSuma = new Particle\Apps\testingController;

      $resut = $cSuma->suma(2,1);

      $this->assertEquals(3, $resut);
    }

}
