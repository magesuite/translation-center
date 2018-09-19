<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Controller\Adminhtml\Import;

use MageSuite\TranslationCenter\Controller\Adminhtml\Import\Index as IndexController;
use MageSuite\TranslationCenter\Test\TestCase;

class IndexTest extends TestCase
{
    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var IndexController
     */
    protected $controllerInstance;

    protected function setUp()
    {
        $this->resultPageMock = $this->getMockBuilder(\Magento\Backend\Model\View\Result\Page::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject $appContextStub */
        $appContextStub = $this->getMockBuilder(\Magento\Backend\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject $pageFactoryStub */
        $pageFactoryStub = $this->getMockBuilder(\Magento\Framework\View\Result\PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pageFactoryStub->method('create')
            ->willReturn($this->resultPageMock);

        $this->controllerInstance = new IndexController($appContextStub, $pageFactoryStub);
    }

    protected function prepareResultPageMockForExecuteMethodCall()
    {
        /** @var \Magento\Framework\View\Page\Title|\PHPUnit_Framework_MockObject_MockObject $titleStub */
        $titleStub = $this->getMockBuilder(\Magento\Framework\View\Page\Title::class)
            ->disableOriginalConstructor()
            ->getMock();

        $titleStub->expects($this->atLeastOnce())
            ->method('prepend')
            ->with($this->isInstanceOf(\Magento\Framework\Phrase::class));

        /** @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject $configStub */
        $configStub = $this->getMockBuilder(\Magento\Framework\View\Page\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configStub->method('getTitle')
            ->willReturn($titleStub);

        $this->resultPageMock
            ->method('getConfig')
            ->willReturn($configStub);

        $this->resultPageMock
            ->expects($this->once())
            ->method('setActiveMenu')
            ->with($this->isType('string'));
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function executeMethodCall()
    {
        return $this->controllerInstance->execute();
    }

    public function itCanBeInstantiated()
    {
        $this->assertInstanceOf(IndexController::class, $this->controllerInstance);
    }

    public function testExecuteMethodReturnsPageInstance()
    {
        $this->prepareResultPageMockForExecuteMethodCall();
        $this->assertInstanceOf(\Magento\Framework\View\Result\Page::class, $this->executeMethodCall());
    }
}
