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
    /**
     * Sample testing function to send Request with POST data
     * @return void
     */
    public function testRequestPOST()
    {
        try {
            // Configuration request with POST parameters a = 1, b = 2
            $objRequestIndex = new Core\Request('index/index', 'POST', array('a'=>1,'b'=>2));
            // Execute the Request, simulate the execution of a web request to the configured Request
            Core\Bootstrap::run($objRequestIndex);
            // Clean super global variable [GET and POST] configured for the previous Request
            $objRequestIndex->closeRequestTesting();
            // Verify that the request performs the action by printing a json, changing some value in DB or printing an HTML
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('Exception: '.$e->getMessage());
        }
    }
}
