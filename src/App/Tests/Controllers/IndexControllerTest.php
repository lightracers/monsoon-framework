<?php

/**
 * IndexController test case.
 */

namespace App\Tests;

use Config\Config;
use App\Controllers\IndexController;
use Framework\Box;
use Framework\Container;
use Framework\Locale;
use Framework\View;

class IndexControllerTest extends BaseTestCase
{

    /**  @var \Controllers\IndexController */
    private $indexController;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp(): void
    {
        // Setup
        parent::setUp();

        // Mocks
        Box::$config =[
            'application' =>[
                'title'    => 'test',
                'language' => 'en',
                'layout'   => 'default',
            ],
        ];

        // Expectations
        $this->configMock->expects($this->exactly(1))
            ->method('getApplication')
            ->willReturn(Box::$config['application']);

        $this->containerMock->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['Framework\View'], ['Framework\Locale'])
            ->willReturnOnConsecutiveCalls(
                new View(),
                new Locale()
            );

        // Init
        $this->indexController = new IndexController($this->configMock);
        $this->indexController->invokeController($this->containerMock);
    }

    /**
     * Tests IndexController->indexAction()
     */
    public function testIndexAction()
    {
        // Calls
        ob_start();
        $this->indexController->indexAction();
        $html = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('</html>', $html);
    }

    /**
     * Tests IndexController->subpageAction()
     */
    public function testSubpageAction()
    {
        // Calls
        ob_start();
        $this->indexController->subpageAction();
        $html = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('</html>', $html);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown(): void
    {
        // TODO Auto-generated IndexControllerTest::tearDown()
        $this->indexController = null;

        parent::tearDown();
    }
}
