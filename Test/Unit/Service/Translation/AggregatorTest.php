<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation;

use MageSuite\TranslationCenter\Service\Translation\Aggregator;
use MageSuite\TranslationCenter\Service\Translation\AggregatorInterface;
use MageSuite\TranslationCenter\Service\Translation\StorageInterface;
use MageSuite\TranslationCenter\Test\TestCase;

class AggregatorTest extends TestCase
{
    /**
     * @var StorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storageMock;

    /**
     * @var Aggregator
     */
    protected $aggregatorInstance;

    protected function setUp()
    {
        $this->storageMock = $this->getMockBuilder(StorageInterface::class)
            ->getMock();

        $this->aggregatorInstance = new Aggregator($this->storageMock);
    }

    public function testAggregatorCanBeInstantiated()
    {
        $this->assertInstanceOf(Aggregator::class, $this->aggregatorInstance);
    }

    public function testAggregatorImplementsAggregatorInterface()
    {
        $this->assertInstanceOf(AggregatorInterface::class, $this->aggregatorInstance);
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testAddSystemTranslationMethodAddsTranslationToStorage(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->storageMock
            ->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo($textToTranslate),
                $this->equalTo($translatedText),
                $this->equalTo($localeCode),
                $this->equalTo($metadata)
            );

        $this->aggregatorInstance->addSystemTranslation($textToTranslate, $translatedText, $localeCode, $metadata);
    }

    /**
     * @param array $translationData
     * @param string $localeCode
     *
     * @dataProvider remoteTranslationDataProvider
     */
    public function testFetchSystemTranslationsReturnsTranslationDataArray(
        array $translationData,
        $localeCode
    ) {
        $this->storageMock
            ->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo($localeCode))
            ->willReturn($translationData);

        $this->assertSame(
            $translationData,
            $this->aggregatorInstance->fetchSystemTranslations($localeCode),
            'Result returned by fetchSystemTranslations() method is different than the expected one!'
        );
    }
}
