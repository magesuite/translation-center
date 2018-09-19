<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Observer\Translation;

use MageSuite\TranslationCenter\Observer\Translation\StoragePersist;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class StoragePersistTest extends TestCase
{
    /**
     * @var \MageSuite\TranslationCenter\Service\Translation\StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storageMock;

    /**
     * @var \MageSuite\TranslationCenter\Service\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var StoragePersist
     */
    protected $storagePersistInstance;

    protected function setUp()
    {
        $this->storageMock = $this->getMockBuilder(
            \MageSuite\TranslationCenter\Service\Translation\StorageInterface::class
        )
            ->getMock();

        $this->configMock = $this->getMockBuilder(\MageSuite\TranslationCenter\Service\ConfigInterface::class)
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->getMock();

        $this->storagePersistInstance = new StoragePersist(
            $this->storageMock,
            $this->configMock,
            $this->loggerMock
        );
    }

    /**
     * @param bool $interceptorActive
     * @param bool $throwException
     */
    protected function prepareMocksAndCallExecuteMethod($interceptorActive, $throwException)
    {
        $throwException = $interceptorActive && $throwException;

        $this->configMock
            ->method('isInterceptorActive')
            ->willReturn($interceptorActive);

        $this->storageMock
            ->expects($interceptorActive ? $this->once() : $this->never())
            ->method('persist');

        if ($throwException) {
            $exception = $this->getMockBuilder(\Exception::class)->getMock();
            $this->storageMock->method('persist')->willThrowException($exception);
            $this->loggerMock
                ->expects($this->once())
                ->method('critical')
                ->with($this->equalTo($exception));
        }

        /** @var \Magento\Framework\Event\Observer|\PHPUnit_Framework_MockObject_MockObject $observerDummy */
        $observerDummy = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->getMock();

        $this->storagePersistInstance->execute($observerDummy);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(StoragePersist::class, $this->storagePersistInstance);
    }

    public function testItImplementsObserverInterface()
    {
        $this->assertInstanceOf(\Magento\Framework\Event\ObserverInterface::class, $this->storagePersistInstance);
    }

    /**
     * @param bool $interceptorActive
     * @param bool $throwException
     * @dataProvider extensionConfigDataProvider
     */
    public function testExecuteMethodCallsPersistOnStorageInterface($interceptorActive, $throwException)
    {
        $this->prepareMocksAndCallExecuteMethod($interceptorActive, $throwException);
    }

    /**
     * @return array
     */
    public function extensionConfigDataProvider()
    {
        return $this->getCartesianProductOfDataValues([
            'interceptor_active' => 'Logical',
            'throw_exception' => 'Logical'
        ]);
    }
}
