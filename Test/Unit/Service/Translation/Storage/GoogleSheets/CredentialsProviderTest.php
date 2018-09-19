<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ConfigInterface;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\CredentialsProvider;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\CredentialsProviderInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\Cache\TypeListInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CredentialsProviderTest extends TestCase
{
    /**
     * @var ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var CredentialsProvider
     */
    protected $credentialsProviderInstance;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp()
    {
        $this->configMock = $this->getMockBuilder(ConfigInterface::class)
            ->getMock();

        $cacheTypeListStub = $this->getMockBuilder(TypeListInterface::class)
            ->getMock();

        $this->credentialsProviderInstance = new CredentialsProvider($this->configMock, $cacheTypeListStub);
    }

    /**
     * @param array $accessToken
     */
    protected function prepareConfigMockForGetAccessTokenCall(array $accessToken)
    {
        $this->configMock
            ->method('getAccessToken')
            ->willReturn($accessToken);
    }

    public function testGoogleSheetsCredentialsProviderCanBeInstantiated()
    {
        $this->assertInstanceOf(CredentialsProvider::class, $this->credentialsProviderInstance);
    }

    public function testGoogleSheetsCredentialsProviderImplementsCredentialsProviderInterface()
    {
        $this->assertInstanceOf(CredentialsProviderInterface::class, $this->credentialsProviderInstance);
    }

    /**
     * @param array $accessToken
     *
     * @dataProvider accessTokenProvider
     */
    public function testGetAccessTokenMethodReturnsAccessTokenArray(array $accessToken)
    {
        $this->prepareConfigMockForGetAccessTokenCall($accessToken);

        $this->assertSame(
            $accessToken,
            $this->credentialsProviderInstance->getAccessToken()
        );
    }

    public function testGetAccessTokenMethodReturnsNullWhenAccessTokenFileDoesNotExist()
    {
        $this->assertNull($this->credentialsProviderInstance->getAccessToken());
    }

    /**
     * @param array $accessToken
     *
     * @dataProvider accessTokenProvider
     */
    public function testSetAccessTokenMethodSavesAccessTokenToFile(array $accessToken)
    {
        $this->configMock
            ->expects($this->once())
            ->method('setAccessToken')
            ->with($this->equalTo($accessToken));

        $this->credentialsProviderInstance->setAccessToken($accessToken);
    }

    /**
     * @return array
     */
    public function accessTokenProvider()
    {
        return [[[
            'access_token' => 'ACCESS_TOKEN',
            'token_type' => 'TOKEN_TYPE',
            'expires_in' => 3600,
            'refresh_token' => 'REFRESH_TOKEN',
            'created' => 1478041433
        ]]];
    }

    /**
     * @return array
     */
    public function secretProvider()
    {
        return [[[
            'installed' => [
                'client_id' => 'CLIENT_ID',
                'project_id' => 'PROJECT_ID',
                'auth_uri' => 'AUTH_URI',
                'token_uri' => 'TOKEN_URI',
                'auth_provider_x509_cert_url' => 'AUTH_PROVIDER_X590_CERT_URL',
                'client_secret' => 'CLIENT_SECRET',
                'redirect_uris' => ['URN', 'URL']
            ]
        ]]];
    }

    /**
     * @return array
     */
    public function authCodeProvider()
    {
        return [
            ['AUTH_CODE']
        ];
    }
}
