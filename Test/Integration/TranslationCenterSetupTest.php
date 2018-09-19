<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class TranslationCenterSetupTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $moduleName = 'MageSuite_TranslationCenter';

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

    public function testTranslationCenterIsRegisteredAsModule()
    {
        /** @var ComponentRegistrar $registrar */
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey(
            $this->moduleName,
            $registrar->getPaths(ComponentRegistrar::MODULE),
            'Translation Center extension is not registered!'
        );
    }

    public function testTranslationCenterIsEnabled()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->create(ModuleList::class);
        $this->assertTrue($moduleList->has($this->moduleName), 'Translation Center extension is not enabled!');
    }
}
