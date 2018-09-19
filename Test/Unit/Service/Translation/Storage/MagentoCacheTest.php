<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\MagentoCache as MagentoCacheStorage;
use MageSuite\TranslationCenter\Service\Translation\Storage\SynchroniseStrategyInterface;
use MageSuite\TranslationCenter\Service\Translation\StorageInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\CacheInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MagentoCacheTest extends TestCase
{
    /**
     * @var CacheInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheMock;

    /**
     * @var SynchroniseStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $synchroniseStrategyMock;

    /**
     * @var MagentoCacheStorage
     */
    protected $storageInstance;

    protected function setUp()
    {
        $this->cacheMock = $this->getMockBuilder(CacheInterface::class)
            ->getMock();

        $this->synchroniseStrategyMock = $this->getMockBuilder(SynchroniseStrategyInterface::class)
            ->getMock();

        $this->storageInstance = new MagentoCacheStorage($this->cacheMock, $this->synchroniseStrategyMock);
    }

    /**
     * @param array|null $translationData
     */
    protected function prepareCacheMockForFetchCall(array $translationData = null)
    {
        $cachedData = $translationData ? json_encode($translationData) : null;
        $this->cacheMock
            ->method('load')
            ->willReturn($cachedData);
    }

    public function testMagentoCacheStorageCanBeInstantiated()
    {
        $this->assertInstanceOf(MagentoCacheStorage::class, $this->storageInstance);
    }

    public function testMagentoCacheStorageImplementsStorageInterface()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->storageInstance);
    }

    /**
     * @param string $localeCode
     *
     * @dataProvider localeProvider
     */
    public function testFetchMethodReturnsEmptyArrayWhenTranslationNotInCache($localeCode)
    {
        $this->prepareCacheMockForFetchCall();
        $this->assertSame([], $this->storageInstance->fetch($localeCode));
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     *
     * @dataProvider translationRowProvider
     */
    public function testFetchMethodReturnsTranslationDataArray(
        $textToTranslate,
        $translatedText,
        $localeCode
    ) {
        $translationData = [[$textToTranslate, $translatedText]];
        $this->prepareCacheMockForFetchCall($translationData);
        $this->assertSame($translationData, $this->storageInstance->fetch($localeCode));
    }
}
