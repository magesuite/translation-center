<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation;

use MageSuite\TranslationCenter\Service\Translation\MetadataProviderInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\ObjectManager\ConfigInterface as ObjectManagerConfig;
use Magento\TestFramework\ObjectManager;

class MetadataProviderInterfaceTest extends TestCase
{
    public function testPreferenceForTranslationMetadataProviderInterfaceIsSet()
    {
        /** @var ObjectManagerConfig $diConfig */
        $diConfig = ObjectManager::getInstance()->get(ObjectManagerConfig::class);
        $this->assertNotEquals(
            MetadataProviderInterface::class,
            $diConfig->getPreference(MetadataProviderInterface::class)
        );
    }
}
