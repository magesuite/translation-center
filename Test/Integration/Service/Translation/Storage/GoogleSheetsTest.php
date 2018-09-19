<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.LongVariables)
 */
class GoogleSheetsTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Google_Service_Sheets|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleServiceSheetsMock;

    protected function setUp()
    {
        $this->markTestSkipped();
        $this->objectManager = ObjectManager::getInstance();
        $this->googleServiceSheetsMock = $this->getMockBuilder(\Google_Service_Sheets::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->objectManager->addSharedInstance($this->googleServiceSheetsMock, 'googleServiceSheets');
    }

    protected function prepareSpreadsheetsPropertyForGoogleServiceSheetsMock()
    {
        /** @var \Google_Service_Sheets_Resource_Spreadsheets|\PHPUnit_Framework_MockObject_MockObject $resourceSpreadsheetsMock */
        $resourceSpreadsheetsMock = $this->getMockBuilder(\Google_Service_Sheets_Resource_Spreadsheets::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Google_Service_Sheets_Spreadsheet|\PHPUnit_Framework_MockObject_MockObject $spreadsheetMock */
        $spreadsheetMock = $this->getMockBuilder(\Google_Service_Sheets_Spreadsheet::class)
            ->getMock();

        $sheetMocks = [];
        foreach (json_decode($this->getJsonData('GoogleApi/responses/spreadsheets/get')) as $sheetFixture) {
            $sheetMock = $this->getMockBuilder(\Google_Service_Sheets_Sheet::class)
                ->getMock();
            $sheetPropertiesMock = $this->getMockBuilder(\Google_Service_Sheets_SheetProperties::class)
                ->getMock();
            $sheetPropertiesMock->method('getTitle')
                ->willReturn($sheetFixture->title);
            $sheetMock->method('getProperties')
                ->willReturn($sheetPropertiesMock);
            $sheetMocks[] = $sheetMock;
        }

        $spreadsheetMock->method('getSheets')
            ->willReturn($sheetMocks);

        $resourceSpreadsheetsMock->method('get')
            ->willReturn($spreadsheetMock);

        $this->googleServiceSheetsMock->spreadsheets = $resourceSpreadsheetsMock;
    }

    protected function prepareSpreadsheetsValuesPropertyForGoogleServiceSheetsMock()
    {
        /** @var \Google_Service_Sheets_Resource_SpreadsheetsValues|\PHPUnit_Framework_MockObject_MockObject $resourceSpreadsheetsValuesMock */
        $resourceSpreadsheetsValuesMock = $this->getMockBuilder(\Google_Service_Sheets_Resource_SpreadsheetsValues::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Google_Service_Sheets_ValueRange||\PHPUnit_Framework_MockObject_MockObject $valueRangeMock */
        $valueRangeMock = $this->getMockBuilder(\Google_Service_Sheets_ValueRange::class)
            ->getMock();

        $valueRangeMock->method('getValues')
            ->willReturn([]);

        $resourceSpreadsheetsValuesMock->method('get')
            ->willReturn($valueRangeMock);

        $this->googleServiceSheetsMock->spreadsheets_values = $resourceSpreadsheetsValuesMock;
    }

    protected function prepareGoogleServiceSheetsMockForPersistMethodCall()
    {
        $this->prepareSpreadsheetsPropertyForGoogleServiceSheetsMock();
        $this->prepareSpreadsheetsValuesPropertyForGoogleServiceSheetsMock();
        $this->googleServiceSheetsMock->spreadsheets_values
            ->expects($this->once())
            ->method('update');
    }

    /**
     * @param array $dictionary
     * @param string $locale
     * @dataProvider storageDictionaryDataProvider
     */
    public function testItSavesTranslationsToGoogleSheets(array $dictionary, $locale)
    {
        $this->prepareGoogleServiceSheetsMockForPersistMethodCall();
        /** @var GoogleSheets $googleSheetsStorage */
        $googleSheetsStorage = $this->objectManager->create(GoogleSheets::class);
        foreach ($dictionary as $row) {
            $googleSheetsStorage->add($row['text_to_translate'], $row['translated_text'], $locale, []);
        }
        $googleSheetsStorage->persist();
    }

    /**
     * @return array
     */
    public function storageDictionaryDataProvider()
    {
        return $this->getCartesianProductOfDataValues([
            'dictionary' => 'Dictionary',
            'locale' => 'Locale'
        ]);
    }
}
