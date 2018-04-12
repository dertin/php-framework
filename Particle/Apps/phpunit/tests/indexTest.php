<?php

namespace Particle\Apps\Test;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint;
use Particle\Apps\Controllers;

/**
 * @covers Particle\Apps\testingController
 */
final class TestingControllerTest extends TestCase
{
    protected $view;

    public function setup()
    {
        $this->view = new \Smarty();
    }

    // public function testBootstrapDefault()
    // {
    //     try {
    //         Particle\Core\Bootstrap::run(new Particle\Core\Request('testing/testing'));
    //         $this->assertTrue(true);
    //     } catch (\Exception $e) {
    //         $this->fail('Exception: '.$e->getMessage());
    //     }
    // }
    //
    // public function testSumar()
    // {
    //     $cSuma = new Particle\Apps\testingController;
    //
    //     $resut = $cSuma->suma(2, 1);
    //
    //     $this->assertEquals(3, $resut);
    // }

    public function testORM()
    {
        try {
            $objIndexController = new Controllers\indexController;
            //$aResult = $objIndexController->testORM();

            $this->assertInternalType(Constraint\IsType::TYPE_INT, $aResult['PersonId']);
            $this->assertNotEmpty($aResult['PersonId']);

            $this->assertNotEmpty($aResult['BookTitle']);
        } catch (\Exception $e) {
            $this->fail('Exception: '.$e->getMessage());
        }
    }
}
