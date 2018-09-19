<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Controller\Adminhtml\GoogleSheets;

use MageSuite\TranslationCenter\Controller\Adminhtml\GoogleSheets\AuthCallback as AuthCallbackController;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AuthCallbackTest extends TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleClientAdapterMock;

    /**
     * @var AuthCallbackController
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

        $this->helperMock = $this->getMockBuilder(\Magento\Backend\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appContextStub->method('getHelper')
            ->willReturn($this->helperMock);

        $messageManagerStub = $this->getMockBuilder(\Magento\Framework\Message\ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appContextStub->method('getMessageManager')
            ->willReturn($messageManagerStub);

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

        $this->controllerInstance = new AuthCallbackController($appContextStub, $this->googleClientAdapterMock);
    }

    protected function prepareMocksForExecuteMethodCall($authCode, $redirectUri)
    {
        $this->requestMock
            ->method('getParam')
            ->will($this->returnValueMap([['code', null, $authCode], ['error', '', '']]));

        $this->helperMock
            ->method('getUrl')
            ->willReturn($redirectUri);

        if ($authCode) {
            $this->googleClientAdapterMock
                ->expects($this->atLeastOnce())
                ->method('authenticate')
                ->with($this->equalTo($authCode), $this->equalTo($redirectUri));
        }
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(AuthCallbackController::class, $this->controllerInstance);
    }

    /**
     * @param $authCode
     * @param $redirectUri
     * @dataProvider authCodeAndRedirectUriDataProvider
     */
    public function testExecuteMethodAuthenticatesAndReturnsRedirectResult($authCode, $redirectUri)
    {
        $this->prepareMocksForExecuteMethodCall($authCode, $redirectUri);
        $this->assertInstanceOf(
            \Magento\Framework\Controller\Result\Redirect::class,
            $this->controllerInstance->execute()
        );
    }

    /**
     * @return array
     */
    public function authCodeAndRedirectUriDataProvider()
    {
        return $this->getCartesianProductOfDataValues([
            'auth_code' => 'GoogleApi/AuthCode',
            'redirect_uri' => 'GoogleApi/RedirectUri'
        ]);
    }
}
