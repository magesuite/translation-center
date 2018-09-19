<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Controller\Adminhtml\GoogleSheets;

use MageSuite\TranslationCenter\Controller\Adminhtml\GoogleSheets\Unlink as UnlinkController;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class UnlinkTest extends TestCase
{
    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleClientAdapterMock;

    /**
     * @var UnlinkController
     */
    protected $controllerInstance;

    protected function setUp()
    {
        /** @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject $appContextStub */
        $appContextStub = $this->getMockBuilder(\Magento\Backend\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appContextStub->method('getRequest')
            ->willReturn($this->requestMock);

        /**
         * @var \Magento\Framework\Controller\Result\RedirectFactory|\PHPUnit_Framework_MockObject_MockObject $redirectFactoryStub
         */
        $redirectFactoryStub = $this->getMockBuilder(\Magento\Framework\Controller\Result\RedirectFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appContextStub->method('getResultRedirectFactory')
            ->willReturn($redirectFactoryStub);

        /**
         * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject $redirectResultStub
         */
        $redirectResultStub = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $redirectResultStub->method('setPath')->willReturnSelf();

        $redirectFactoryStub->method('create')
            ->willReturn($redirectResultStub);

        $this->googleClientAdapterMock = $this->getMockBuilder(
            \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->controllerInstance = new UnlinkController($appContextStub, $this->googleClientAdapterMock);
    }

    /**
     * @param bool $revokeToken
     */
    protected function prepareMocksForExecuteMethodCall($revokeToken)
    {
        $this->requestMock
            ->method('getParam')
            ->willReturn($revokeToken);

        $this->googleClientAdapterMock
            ->expects($this->once())
            ->method('unlink');

        if ($revokeToken) {
            $this->googleClientAdapterMock
                ->expects($this->once())
                ->method('revokeToken');
        }
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(UnlinkController::class, $this->controllerInstance);
    }

    /**
     * @param bool $revokeTokens
     * @dataProvider revokeTokensDataProvider
     */
    public function testExecuteMethodUnlinksAndReturnsRedirectResult($revokeTokens)
    {
        $this->prepareMocksForExecuteMethodCall($revokeTokens);
        $this->assertInstanceOf(
            \Magento\Framework\Controller\Result\Redirect::class,
            $this->controllerInstance->execute()
        );
    }

    /**
     * @return array
     */
    public function revokeTokensDataProvider()
    {
        return [
            ['revoke_tokens' => true],
            ['revoke_tokens' => false]
        ];
    }
}
