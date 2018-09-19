<?php
/**
 * Magento 2 Constructor
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation;

use MageSuite\TranslationCenter\Service\Translation\ImporterInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use Magento\TestFramework\ObjectManager;

class ImporterInterfaceTest extends TestCase
{
    public function testPreferenceForTranslationStorageInterfaceIsSet()
    {
        /** @var ObjectManagerConfig $diConfig */
        $diConfig = ObjectManager::getInstance()->get(ObjectManagerConfig::class);
        $this->assertNotEquals(ImporterInterface::class, $diConfig->getPreference(ImporterInterface::class));
    }
}
