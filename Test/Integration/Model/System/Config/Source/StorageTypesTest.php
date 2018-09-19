<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Model\System\Config\Source;

use MageSuite\TranslationCenter\Model\System\Config\Source\StorageTypes;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\TestFramework\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.LongVariables)
 */
class StorageTypesTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected $storageClasses = [
        \MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets::class,
        \MageSuite\TranslationCenter\Service\Translation\Storage\Csv::class,
        \MageSuite\TranslationCenter\Service\Translation\Storage\MagentoCache::class
    ];

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @param array $options
     * @return array
     */
    protected function extractOptionValues(array $options)
    {
        return array_map(
            function ($option) {
                return $option['value'];
            },
            $options
        );
    }

    public function testToOptionArrayMethodReturnsArrayOfStorageClasses()
    {
        /** @var StorageTypes $storageTypesSourceModel */
        $storageTypesSourceModel = $this->objectManager->create(StorageTypes::class);
        $optionValues = $this->extractOptionValues($storageTypesSourceModel->toOptionArray());
        $this->assertEmpty(array_diff($this->storageClasses, $optionValues));
    }
}
