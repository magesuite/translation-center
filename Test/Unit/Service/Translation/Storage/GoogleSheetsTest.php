<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Facade as GoogleSheetsFacade;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;
use MageSuite\TranslationCenter\Service\Translation\Storage\SynchroniseStrategyInterface;
use MageSuite\TranslationCenter\Service\Translation\StorageInterface;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GoogleSheetsTest extends TestCase
{
    /**
     * @var GoogleSheetsFacade|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleSheetsFacadeMock;

    /**
     * @var SynchroniseStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $synchroniseStrategyMock;

    /**
     * @var GoogleSheets
     */
    protected $googleSheetsStorageInstance;

    protected function setUp()
    {
        $this->googleSheetsFacadeMock = $this->getMockBuilder(GoogleSheetsFacade::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->synchroniseStrategyMock = $this->getMockBuilder(SynchroniseStrategyInterface::class)
            ->getMock();

        $this->googleSheetsStorageInstance = new GoogleSheets(
            $this->googleSheetsFacadeMock,
            $this->synchroniseStrategyMock
        );
    }

    /**
     * @param string $localeCode
     * @param string $sheetRange
     * @param array $translationData
     */
    protected function prepareGoogleSheetFacadeMockForGetValuesMethodCall(
        $localeCode,
        $sheetRange,
        array $translationData
    ) {
        $this->googleSheetsFacadeMock
            ->expects($this->once())
            ->method('getValues')
            ->with($this->equalTo($localeCode), $this->equalTo($sheetRange))
            ->willReturn($translationData);
    }

    /**
     * @param $textToTranslate
     * @param $translatedText
     * @param $localeCode
     * @param array $metadata
     */
    protected function prepareMocksForFlushMethodCall($textToTranslate, $translatedText, $localeCode, array $metadata)
    {
        $sheetRange = sprintf('A1:%s', chr(ord('A') + count($metadata) + 1));
        $translationData = [array_merge([$textToTranslate, $translatedText], $metadata)];

        $this->prepareGoogleSheetFacadeMockForGetValuesMethodCall($localeCode, $sheetRange, $translationData);

        $this->synchroniseStrategyMock
            ->expects($this->once())
            ->method('synchronise')
            ->with($this->equalTo($translationData), $this->equalTo($translationData))
            ->willReturn($translationData);

        $this->googleSheetsFacadeMock
            ->expects($this->once())
            ->method('updateRows')
            ->with(
                $this->equalTo($localeCode),
                $this->equalTo($sheetRange),
                $this->equalTo($translationData)
            );
    }

    public function testGoogleSheetsStorageCanBeInstantiated()
    {
        $this->assertInstanceOf(GoogleSheets::class, $this->googleSheetsStorageInstance);
    }

    public function testGoogleSheetsStorageImplementsStorageInterface()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->googleSheetsStorageInstance);
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testPersistMethodSavesStoredDataToAppropriateSheet(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->googleSheetsStorageInstance->add($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->prepareMocksForFlushMethodCall($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->googleSheetsStorageInstance->persist();
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testPersistMethodCreatesSheetIfItDoesNotExist(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->googleSheetsStorageInstance->add($textToTranslate, $translatedText, $localeCode, $metadata);

        $this->googleSheetsFacadeMock
            ->expects($this->once())
            ->method('sheetExists')
            ->with($this->equalTo($localeCode))
            ->willReturn(false);

        $this->googleSheetsFacadeMock
            ->expects($this->once())
            ->method('createSheet')
            ->with($this->equalTo($localeCode));

        $this->prepareMocksForFlushMethodCall($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->googleSheetsStorageInstance->persist();
        $this->assertAttributeEquals([], 'storageData', $this->googleSheetsStorageInstance);
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testPersistMethodDoesNotCreateSheetIfItExists(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->googleSheetsStorageInstance->add($textToTranslate, $translatedText, $localeCode, $metadata);

        $this->googleSheetsFacadeMock
            ->expects($this->once())
            ->method('sheetExists')
            ->with($this->equalTo($localeCode))
            ->willReturn(true);

        $this->googleSheetsFacadeMock
            ->expects($this->never())
            ->method('createSheet');

        $this->prepareMocksForFlushMethodCall($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->googleSheetsStorageInstance->persist();
        $this->assertAttributeEquals([], 'storageData', $this->googleSheetsStorageInstance);
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
        $this->prepareGoogleSheetFacadeMockForGetValuesMethodCall($localeCode, 'A1:B', $translationData);
        $this->googleSheetsStorageInstance->fetch($localeCode);
    }
}
