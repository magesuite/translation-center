<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Controller\Adminhtml\GoogleSheets;

use MageSuite\TranslationCenter\Controller\Adminhtml\GoogleSheets\Auth as AuthController;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AuthTest extends TestCase
{
    /**
     * @var AuthController
     */
    protected $controllerInstance;

    protected function setUp()
    {
        /** @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject $helperStub */
        $backendUrlStub = $this->getMockBuilder(\Magento\Backend\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $backendUrlStub->method('getUrl')
            ->willReturn('http://example.com');

        /** @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject $redirectResultStub */
        $redirectResultStub = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redirectResultStub->method('setUrl')
            ->willReturn($redirectResultStub);

        /** @var \Magento\Framework\Controller\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject $redirectFactoryStub */
        $redirectFactoryStub = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redirectFactoryStub->method('create')
            ->willReturn($redirectResultStub);

        /** @var \Magento\Backend\App\Action|\PHPUnit_Framework_MockObject_MockObject $appContextStub */
        $appContextStub = $this->getMockBuilder(\Magento\Backend\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appContextStub->method('getBackendUrl')
            ->willReturn($backendUrlStub);

        $appContextStub->method('getResultRedirectFactory')
            ->willReturn($redirectFactoryStub);

        /** @var \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client|\PHPUnit_Framework_MockObject_MockObject $googleClientAdapterStub */
        $googleClientAdapterStub = $this->getMockBuilder(
            \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $googleClientAdapterStub->method('getAuthUrl')
            ->willReturn('http://example.com');

        $this->controllerInstance = new AuthController($appContextStub, $googleClientAdapterStub);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(AuthController::class, $this->controllerInstance);
    }

    public function testExecuteMethodReturnsRedirectResult()
    {
        // @todo check if google clients generates auth URL
        $result = $this->controllerInstance->execute();
        $this->assertInstanceOf(\Magento\Framework\Controller\Result\Redirect::class, $result);
    }
}
