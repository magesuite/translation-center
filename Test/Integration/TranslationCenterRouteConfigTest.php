<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration;

use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\Route\ConfigInterface as RouteConfigInterface;
use Magento\TestFramework\ObjectManager;

class TranslationCenterRouteConfigTest extends TestCase
{
    /**
     * @var string
     */
    protected $frontName = 'translation_center';

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
     * @magentoAppArea adminhtml
     */
    public function testAdminRouteIsConfigured()
    {
        /** @var RouteConfigInterface $routeConfig */
        $routeConfig = $this->objectManager->create(RouteConfigInterface::class);
        $this->assertContains('MageSuite_TranslationCenter', $routeConfig->getModulesByFrontName($this->frontName));
    }
}
