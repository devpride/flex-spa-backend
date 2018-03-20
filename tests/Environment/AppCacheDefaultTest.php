<?php
declare(strict_types=1);

namespace App\Tests\Environment;

use App\Service\Cache\AppCacheDefault;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class AppCacheDefaultTest
 */
class AppCacheDefaultTest extends KernelTestCase
{
    /**
     * @var AppCacheDefault
     */
    private $appCacheDefault;

    public function setUp()
    {
        $this->appCacheDefault = $this->bootKernel()->getContainer()->get('test.App\Service\Cache\AppCacheDefault');
        $this->appCacheDefault->clear();
    }

    public function tearDown()
    {
        $this->appCacheDefault->clear();
    }

    public function testConnectionSuccess()
    {
        $expectedValue = 42;
        $this->assertFalse($this->appCacheDefault->hasItem('test'));

        $testItem = $this->appCacheDefault->getItem('test');
        $testItem->set($expectedValue);

        $this->assertTrue($this->appCacheDefault->save($testItem));
        $this->assertTrue($this->appCacheDefault->hasItem('test'));
        $this->assertEquals($expectedValue, $this->appCacheDefault->getItem('test')->get());
    }
}
