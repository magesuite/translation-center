<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\CredentialsProviderInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\TestFramework\ObjectManager;

class ClientTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @magentoCache config disabled
     */
    public function testUnlinkMethodClearsAccessTokenInCredentialsProvider()
    {
        $this->markTestSkipped();
        $credentialsProvider = $this->objectManager->get(CredentialsProviderInterface::class);
        $credentialsProvider->setAccessToken([
            'access_token' => 'ACCESS_TOKEN',
            'token_type' => 'TOKEN_TYPE',
            'expires_in' => 3600,
            'refresh_token' => 'REFRESH_TOKEN',
            'created' => time()
        ]);
        /** @var Client $clientAdapter */
        $clientAdapter = $this->objectManager->get(Client::class);
        $clientAdapter->unlink();
        $this->assertNull($credentialsProvider->getAccessToken());
    }
}
