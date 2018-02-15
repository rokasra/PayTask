<?php

namespace App\Tests;

use App\Service\CommissionsManager;

/**
 * App\Tests\CommissionCalculatorManagerTest
 */
class CommissionCalculatorManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CommissionsManager
     */
    protected $manager;

    /**
     * Test Calculate method
     * */
    public function testCalculate()
    {
        $this->assertTrue($this->manager->calculate());
    }

    /**
     * Sets up test
     */
    protected function setUp()
    {
        $this->manager = new CommissionsManager();
    }
}