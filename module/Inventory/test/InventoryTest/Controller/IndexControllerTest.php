<?php

namespace InventoryTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class InventoryControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp()
    {
        $this->setApplicationConfig(
            include '/var/www/html/MLIB-Inventory-Server/module/Inventory/config/module.config.php'
        );
        parent::setUp();
    }

  public function testComputerIndexAction()
  {
    $this->dispatch('/computer');
    $this->assertResponseStatusCode(200);
    $this->assertModuleName('Inventory');
    $this->assertControllerName('Album\Controller\Computer');
    $this->assertControllerClass('ComputerController');
    $this->assertMatchedRouteName('computer');
  }
}
