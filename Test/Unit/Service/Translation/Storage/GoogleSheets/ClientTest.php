<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ConfigInterface as GoogleSheetsConfig;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\CredentialsProviderInterface;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariables)
 */
class ClientTest extends TestCase
{
    protected $googleClientMock;
    protected $googleSheetsCredentialsMock;
    protected $googleSheetsConfigMock;
    /**
     * @var Client
     */
    protected $clientAdapterInstance;

    protected function setUp()
    {
        /** @var \Google_Client|\PHPUnit_Framework_MockObject_MockObject $googleClientMock */
        $this->googleClientMock = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var CredentialsProviderInterface|\PHPUnit_Framework_MockObject_MockObject $credentialsMock */
        $this->googleSheetsCredentialsMock = $this->getMockBuilder(CredentialsProviderInterface::class)
            ->getMock();

        $this->googleSheetsCredentialsMock
            ->method('getSecret')
            ->willReturn([
                'installed' => [
                    'client_id' => 'CLIENT_ID',
                    'project_id' => 'PROJECT_ID',
                    'auth_uri' => 'AUTH_URI',
                    'token_uri' => 'TOKEN_URI',
                    'auth_provider_x509_cert_url' => 'AUTH_PROVIDER_X590_CERT_URL',
                    'client_secret' => 'CLIENT_SECRET',
                    'redirect_uris' => ['URN', 'URL']
                ]
            ]);

        $this->googleSheetsCredentialsMock
            ->method('getAccessToken')
            ->willReturn([
                'access_token' => 'ACCESS_TOKEN',
                'token_type' => 'TOKEN_TYPE',
                'expires_in' => 3600,
                'refresh_token' => 'REFRESH_TOKEN',
                'created' => time()
            ]);

        /** @var GoogleSheetsConfig|\PHPUnit_Framework_MockObject_MockObject $googleSheetsConfigMock */
        $this->googleSheetsConfigMock = $this->getMockBuilder(GoogleSheetsConfig::class)
            ->getMock();

        $this->googleSheetsConfigMock
            ->method('getApplicationName')
            ->willReturn('APPLICATION_NAME');

        $this->googleSheetsConfigMock
            ->method('getScopes')
            ->willReturn(['SCOPE1', 'SCOPE2']);

        $this->clientAdapterInstance = new Client(
            $this->googleClientMock,
            $this->googleSheetsCredentialsMock,
            $this->googleSheetsConfigMock
        );
    }

    public function testClientAdapterCanBeInstantiated()
    {
        $this->assertInstanceOf(Client::class, $this->clientAdapterInstance);
    }

    public function testClientAdapterCallsSetAccessTokenOnGoogleClientObject()
    {
        $this->googleClientMock->expects($this->once())
            ->method('setApplicationName')
            ->with($this->isType('string'));

        $this->googleClientMock->expects($this->once())
            ->method('setScopes')
            ->with($this->isType('string'));

        $this->googleClientMock->expects($this->once())
            ->method('setAuthConfig')
            ->with($this->isType('array'));

        $this->googleClientMock->expects($this->once())
            ->method('setAccessToken')
            ->with($this->isType('array'));

        /** @var \ReflectionClass $googleClientAdapterReflection */
        $googleClientAdapterReflection = new \ReflectionClass(Client::class);
        $googleClientAdapterConstructor = $googleClientAdapterReflection->getConstructor();
        $googleClientAdapterConstructor->invoke(
            $this->clientAdapterInstance,
            $this->googleClientMock,
            $this->googleSheetsCredentialsMock,
            $this->googleSheetsConfigMock
        );
    }

    public function testClientAdapterRefreshesAccessTokenWhenItExpires()
    {
        $refreshToken = 'REFRESH_TOKEN';
        $newAccessToken = ['access_token' => 'ACCESS_TOKEN'];

        $this->googleClientMock
            ->method('isAccessTokenExpired')
            ->willReturn(true);

        $this->googleClientMock->expects($this->exactly(2))
            ->method('getRefreshToken')
            ->willReturn($refreshToken);

        $this->googleClientMock->expects($this->once())
            ->method('fetchAccessTokenWithRefreshToken')
            ->with($this->equalTo($refreshToken));

        $this->googleClientMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($newAccessToken);

        $this->googleSheetsCredentialsMock
            ->expects($this->once())
            ->method('setAccessToken')
            ->with($newAccessToken);

        /** @var \ReflectionClass $googleClientAdapterReflection */
        $googleClientAdapterReflection = new \ReflectionClass(Client::class);
        $googleClientAdapterConstructor = $googleClientAdapterReflection->getConstructor();
        $googleClientAdapterConstructor->invoke(
            $this->clientAdapterInstance,
            $this->googleClientMock,
            $this->googleSheetsCredentialsMock,
            $this->googleSheetsConfigMock
        );
    }
}
