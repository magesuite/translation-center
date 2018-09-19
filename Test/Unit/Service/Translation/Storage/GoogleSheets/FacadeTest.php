<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ConfigInterface as GoogleSheetsConfig;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\CredentialsProviderInterface;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\RequestFactory;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ValueRangeFactory;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Facade as GoogleSheetsFacade;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FacadeTest extends TestCase
{
    /**
     * @var Client
     */
    protected $googleClientAdapterMock;

    /**
     * @var \Google_Client|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleClientMock;

    /**
     * @var \Google_Service_Sheets|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleSheetsServiceMock;

    /**
     * @var \Google_Service_Sheets_Resource_Spreadsheets|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spreadsheetsResourceMock;

    /**
     * @var \Google_Service_Sheets_Resource_SpreadsheetsSheets|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spreadsheetsSheetsResourceMock;

    /**
     * @var \Google_Service_Sheets_Resource_SpreadsheetsValues|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spreadsheetsValuesResourceMock;

    /**
     * @var \Google_Service_Sheets_Spreadsheet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $spreadsheetMock;

    /**
     * @var GoogleSheetsConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleSheetsConfigMock;

    /**
     * @var CredentialsProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleSheetsCredentialsMock;

    /**
     * @var RequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestFactoryMock;

    /**
     * @var ValueRangeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueRangeFactoryMock;

    /**
     * @var GoogleSheetsFacade
     */
    protected $facadeInstance;

    protected function setUp()
    {
        $this->googleClientMock = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->spreadsheetsResourceMock = $this->getMockBuilder(\Google_Service_Sheets_Resource_Spreadsheets::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->spreadsheetsSheetsResourceMock = $this->getMockBuilder(
            \Google_Service_Sheets_Resource_SpreadsheetsSheets::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->spreadsheetsValuesResourceMock = $this->getMockBuilder(
            \Google_Service_Sheets_Resource_SpreadsheetsValues::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->googleSheetsServiceMock = $this->getMockBuilder(\Google_Service_Sheets::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->googleSheetsServiceMock->spreadsheets = $this->spreadsheetsResourceMock;
        $this->googleSheetsServiceMock->spreadsheets_sheets = $this->spreadsheetsSheetsResourceMock;
        $this->googleSheetsServiceMock->spreadsheets_values = $this->spreadsheetsValuesResourceMock;

        $this->googleSheetsConfigMock = $this->getMockBuilder(GoogleSheetsConfig::class)
            ->getMock();

        $this->googleSheetsConfigMock
            ->method('getSpreadsheetId')
            ->willReturn('SPREADSHEET_ID');

        $this->googleSheetsConfigMock
            ->method('getApplicationName')
            ->willReturn('APPLICATION_NAME');

        $this->googleSheetsConfigMock
            ->method('getScopes')
            ->willReturn(['SCOPE1', 'SCOPE2']);

        $this->googleSheetsCredentialsMock = $this->getMockBuilder(CredentialsProviderInterface::class)
            ->getMock();

        $this->googleSheetsCredentialsMock
            ->method('getSecret')
            ->willReturn([
                'installed' => [
                    'client_id' => 'CLIENT_ID',
                    'project_id' => 'PROJECT_ID',
                    'auth_uri' => 'AUTH_URI',
                    'token_uri' => 'TOKEN_URI',
                    'auth_provider_x509_cert_url' => 'AUTH_PROVIDER_X590_CERT_URL',
                    'client_secret' => 'CLIENT_SECRET',
                    'redirect_uris' => ['URN', 'URL']
                ]
            ]);

        $this->googleSheetsCredentialsMock
            ->method('getAccessToken')
            ->willReturn([
                'access_token' => 'ACCESS_TOKEN',
                'token_type' => 'TOKEN_TYPE',
                'expires_in' => 3600,
                'refresh_token' => 'REFRESH_TOKEN',
                'created' => 1478041433
            ]);

        $this->requestFactoryMock = $this->getMockBuilder(RequestFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->valueRangeFactoryMock = $this->getMockBuilder(ValueRangeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->spreadsheetMock = $this->getMockBuilder(\Google_Service_Sheets_Spreadsheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Client $clientAdapterStub */
        $this->googleClientAdapterMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facadeInstance = new GoogleSheetsFacade(
            $this->googleSheetsServiceMock,
            $this->googleClientAdapterMock,
            $this->googleSheetsConfigMock,
            $this->requestFactoryMock,
            $this->valueRangeFactoryMock
        );
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @return string
     */
    protected function getFullRangeAddress($sheetTitle, $range)
    {
        return sprintf('%s!%s', $sheetTitle, $range);
    }

    /**
     * @param array $properties
     * @return \Google_Service_Sheets_SheetProperties|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSheetPropertiesMock(array $properties)
    {
        $sheetPropertiesMock = $this->getMockBuilder(\Google_Service_Sheets_SheetProperties::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sheetPropertiesMock->method('getTitle')
            ->willReturn(array_key_exists('title', $properties) ? $properties['title'] : null);

        return $sheetPropertiesMock;
    }

    /**
     * @param array $sheetProperties
     * @return \Google_Service_Sheets_Sheet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSheetMock(array $sheetProperties)
    {
        $sheetPropertiesMock = $this->getSheetPropertiesMock($sheetProperties);
        $sheetMock = $this->getMockBuilder(\Google_Service_Sheets_Sheet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $sheetMock->method('getProperties')
            ->willReturn($sheetPropertiesMock);

        return $sheetMock;
    }

    /**
     * @param array|null $values
     * @param string|null $fullRangeAddress
     *
     * @return \Google_Service_Sheets_ValueRange|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getValueRangeMock($values, $fullRangeAddress = null)
    {
        $valueRangeMock = $this->getMockBuilder(\Google_Service_Sheets_ValueRange::class)
            ->disableOriginalConstructor()
            ->getMock();
        $valueRangeMock->method('getRange')
            ->willReturn($fullRangeAddress);
        $valueRangeMock->method('getValues')
            ->willReturn($values);
        return $valueRangeMock;
    }

    /**
     * @return \Closure
     */
    protected function getCreateSheetMockCallback()
    {
        return function (array $sheetProperties) {
            return $this->getSheetMock($sheetProperties);
        };
    }

    /**
     * @return \Closure
     */
    protected function getExpandRangeCallback()
    {
        return function ($range) {
            return $this->expandRange($range[0]);
        };
    }

    /**
     * @param array $sheetMocks
     */
    protected function prepareMocksForGetSheetsMethodCall(array $sheetMocks)
    {
        $this->spreadsheetsResourceMock
            ->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->willReturn($this->spreadsheetMock);

        $this->spreadsheetMock
            ->expects($this->once())
            ->method('getSheets')
            ->with()
            ->willReturn($sheetMocks);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array|null $values
     */
    protected function prepareMocksForGetValuesMethodCall($sheetTitle, $range, $values)
    {
        $fullRangeAddress = $this->getFullRangeAddress($sheetTitle, $range);
        $valueRangeMock = $this->getValueRangeMock($values, $fullRangeAddress);
        $this->spreadsheetsValuesResourceMock
            ->expects($this->once())
            ->method('get')
            ->with($this->isType('string'), $this->equalTo($fullRangeAddress))
            ->willReturn($valueRangeMock);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     */
    protected function prepareMocksForUpdateRowsMethodCall($sheetTitle, $range, array $dataRows)
    {
        $fullRangeAddress = $this->getFullRangeAddress($sheetTitle, $range);
        $valueRangeMock = $this->getValueRangeMock($dataRows, $fullRangeAddress);
        $this->valueRangeFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($dataRows))
            ->willReturn($valueRangeMock);

        $this->spreadsheetsValuesResourceMock
            ->expects($this->once())
            ->method('update')
            ->with(
                $this->isType('string'),
                $this->equalTo($fullRangeAddress),
                $this->equalTo($valueRangeMock),
                $this->equalTo(['valueInputOption' => 'RAW'])
            );
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareMocksForAppendRowsMethodCall($sheetTitle, $range, array $dataRows)
    {
        $valueRangeMock = $this->getValueRangeMock($dataRows);

        $this->valueRangeFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($dataRows))
            ->willReturn($valueRangeMock);

        $this->spreadsheetsValuesResourceMock
            ->expects($this->once())
            ->method('append')
            ->with(
                $this->isType('string'),
                $this->stringStartsWith($sheetTitle),
                $this->equalTo($valueRangeMock),
                $this->equalTo(['valueInputOption' => 'RAW'])
            );
    }

    public function testGoogleSheetsFacadeCanBeInstantiated()
    {
        $this->assertInstanceOf(GoogleSheetsFacade::class, $this->facadeInstance);
    }

    /**
     * @param array $sheetMocks
     *
     * @dataProvider sheetMocksArrayProvider
     */
    public function testGetSheetsMethodReturnsArrayOfSheets(array $sheetMocks)
    {
        $this->prepareMocksForGetSheetsMethodCall($sheetMocks);
        $sheets = $this->facadeInstance->getSheets();
        $this->assertInternalType('array', $sheets, 'getSheets() method does not return array!');
        $this->assertContainsOnlyInstancesOf(
            \Google_Service_Sheets_Sheet::class,
            $sheets,
            sprintf('Returned array contains objects of other type than %s!', \Google_Service_Sheets_Sheet::class)
        );
    }

    /**
     * @param array $sheetMocks
     *
     * @dataProvider sheetMocksArrayProvider
     */
    public function testGetSheetsMethodCachesResult(array $sheetMocks)
    {
        $this->prepareMocksForGetSheetsMethodCall($sheetMocks);

        for ($i = 1; $i <= rand(2, 10); $i++) {
            $sheets = $this->facadeInstance->getSheets();
        }

        $this->assertInternalType('array', $sheets, 'getSheets() method does not return array!');
        $this->assertContainsOnlyInstancesOf(
            \Google_Service_Sheets_Sheet::class,
            $sheets,
            sprintf('Returned array contains objects of other type than %s!', \Google_Service_Sheets_Sheet::class)
        );
    }

    /**
     * @param array $sheetMocks
     * @param string $sheetTitle
     *
     * @dataProvider sheetMocksWithExistingTitleArrayProvider
     */
    public function testGetSheetByTitleMethodReturnsExistingSheet(array $sheetMocks, $sheetTitle)
    {
        $this->prepareMocksForGetSheetsMethodCall($sheetMocks);
        $sheet = $this->facadeInstance->getSheetByTitle($sheetTitle);
        $this->assertInstanceOf(
            \Google_Service_Sheets_Sheet::class,
            $sheet,
            'getSheetByTitle() method does not return object of the expected type!'
        );
        $this->assertSame(
            $sheetTitle,
            $sheet->getProperties()->getTitle(),
            'Sheet returned by getSheetByTitle() method has wrong title!'
        );
    }

    /**
     * @param array $sheetMocks
     * @param string $sheetTitle
     *
     * @dataProvider sheetMocksWithNonExistingTitleArrayProvider
     */
    public function testGetSheetByTitleMethodReturnsNullForNonExistingSheet(array $sheetMocks, $sheetTitle)
    {
        $this->prepareMocksForGetSheetsMethodCall($sheetMocks);
        $this->assertNull(
            $this->facadeInstance->getSheetByTitle($sheetTitle),
            'getSheetByTitle() method does not return null for non-existing sheet!'
        );
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $values
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testGetValuesMethodReturnsArrayOfValues($sheetTitle, $range, array $values)
    {
        $this->prepareMocksForGetValuesMethodCall($sheetTitle, $range, $values);
        $getValuesResult = $this->facadeInstance->getValues($sheetTitle, $range);
        $this->assertInternalType('array', $getValuesResult, 'getValues() method does not return array!');
        $this->assertSame(
            $values,
            $getValuesResult,
            'Result returned by getValues() method is different than expected one!'
        );
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testGetValuesMethodReturnsEmptyArrayForNonExistingSheet($sheetTitle, $range)
    {
        $this->prepareMocksForGetValuesMethodCall($sheetTitle, $range, null);
        $getValuesResult = $this->facadeInstance->getValues($sheetTitle, $range);
        $this->assertInternalType('array', $getValuesResult, 'getValues() method does not return array!');
        $this->assertEmpty($getValuesResult, 'Array returned by getValues() method is not empty!');
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $values
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testGetValuesMethodCachesResult($sheetTitle, $range, array $values)
    {
        $this->prepareMocksForGetValuesMethodCall($sheetTitle, $range, $values);

        for ($i = 1; $i <= rand(2, 10); $i++) {
            $getValuesResult = $this->facadeInstance->getValues($sheetTitle, $range);
        }

        $this->assertInternalType('array', $getValuesResult, 'getValues() method does not return array!');
        $this->assertSame($values, $getValuesResult, 'Result returned by getValues() is different than expected one!');
    }

    /**
     * @param \Google_Service_Sheets_Sheet|\PHPUnit_Framework_MockObject_MockObject $sheetMock
     * @param string $sheetTitle
     *
     * @dataProvider sheetMockWithTitleProvider
     */
    public function testSheetExistsMethodReturnsTrueForExistingSheets(
        \Google_Service_Sheets_Sheet $sheetMock,
        $sheetTitle
    ) {
        $this->prepareMocksForGetSheetsMethodCall([$sheetMock]);
        $this->assertTrue(
            $this->facadeInstance->sheetExists($sheetTitle),
            'sheetExists() method does not return expected result!'
        );
    }

    /**
     * @param \Google_Service_Sheets_Sheet|\PHPUnit_Framework_MockObject_MockObject $sheetMock
     * @param string $sheetTitle
     *
     * @dataProvider sheetMockWithNonExistingTitleProvider
     */
    public function testSheetExistsMethodReturnsFalseForNonExistingSheets(
        \Google_Service_Sheets_Sheet $sheetMock,
        $sheetTitle
    ) {
        $this->prepareMocksForGetSheetsMethodCall([$sheetMock]);
        $this->assertFalse(
            $this->facadeInstance->sheetExists($sheetTitle),
            'sheetExists() method does not return expected result!'
        );
    }

    /**
     * @param string $sheetTitle
     *
     * @dataProvider sheetProvider
     */
    public function testCreateSheetMethodSubmitAppropriateServiceRequest($sheetTitle)
    {
        $updateSpreadsheetRequestMock = $this->getMockBuilder(
            \Google_Service_Sheets_BatchUpdateSpreadsheetRequest::class
        )->getMock();

        $this->requestFactoryMock
            ->expects($this->once())
            ->method('createBatchUpdateSpreadsheet')
            ->with($this->equalTo(['addSheet' => ['properties' => ['title' => $sheetTitle]]]))
            ->willReturn($updateSpreadsheetRequestMock);

        $this->spreadsheetsResourceMock
            ->expects($this->once())
            ->method('batchUpdate')
            ->with($this->isType('string'), $this->equalTo($updateSpreadsheetRequestMock));

        $this->facadeInstance->createSheet($sheetTitle);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testUpdateRowsMethodSubmitAppropriateServiceRequest($sheetTitle, $range, array $dataRows)
    {
        $this->prepareMocksForUpdateRowsMethodCall($sheetTitle, $range, $dataRows);
        $this->facadeInstance->updateRows($sheetTitle, $range, $dataRows);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testAppendRowsMethodSubmitAppropriateServiceRequest($sheetTitle, $range, array $dataRows)
    {
        $this->prepareMocksForAppendRowsMethodCall($sheetTitle, $range, $dataRows);
        $this->facadeInstance->appendRows($sheetTitle, $range, $dataRows);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRow
     *
     * @dataProvider sheetDataRowProvider
     */
    public function testAppendRowMethodSubmitAppropriateServiceRequest($sheetTitle, $range, array $dataRow)
    {
        $this->prepareMocksForAppendRowsMethodCall($sheetTitle, $range, [$dataRow]);
        $this->facadeInstance->appendRow($sheetTitle, $range, $dataRow);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testFindRowMethodReturnsArrayForExistingString($sheetTitle, $range, array $dataRows)
    {
        $this->prepareMocksForGetValuesMethodCall($sheetTitle, $range, $dataRows);
        $searchedDataRow = reset($dataRows);
        foreach ($searchedDataRow as $searchedString) {
            $this->assertSame(
                $searchedDataRow,
                $this->facadeInstance->findRow($searchedString, $sheetTitle, $range),
                'Array returned by findRow() method is different than expected one!'
            );
        }
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     *
     * @dataProvider rangeAddressComponentsWithValuesProvider
     */
    public function testFindRowMethodReturnsNullForExistingString($sheetTitle, $range, array $dataRows)
    {
        $searchedDataRow = array_shift($dataRows);
        $this->prepareMocksForGetValuesMethodCall($sheetTitle, $range, $dataRows);
        foreach ($searchedDataRow as $searchedString) {
            $this->assertNull(
                $this->facadeInstance->findRow($searchedString, $sheetTitle, $range),
                'findRow() does not return null value for non exisiting string!'
            );
        }
    }

    /**
     * @return array
     */
    public function sheetTitleProvider()
    {
        return [
            ['title' => 'Sheet1'],
            ['title' => 'Sheet2'],
            ['title' => 'Sheet3']
        ];
    }

    /**
     * @return array
     */
    public function nonExistingSheetTitleProvider()
    {
        return [
            ['title' => 'Unknown sheet1'],
            ['title' => 'Unknown sheet2'],
            ['title' => 'Unknown sheet3']
        ];
    }

    /**
     * @return array
     */
    public function sheetProvider()
    {
        return $this->mergeDataProviders(
            $this->sheetTitleProvider()
        );
    }

    /**
     * @return array
     */
    public function sheetMockProvider()
    {
        return $this->mapDataProviders($this->getCreateSheetMockCallback(), $this->sheetProvider());
    }

    /**
     * @return array
     */
    public function sheetMockWithTitleProvider()
    {
        return $this->mergeDataProviders($this->sheetMockProvider(), $this->sheetTitleProvider());
    }

    /**
     * @return array
     */
    public function sheetMockWithNonExistingTitleProvider()
    {
        return $this->mergeDataProviders($this->sheetMockProvider(), $this->nonExistingSheetTitleProvider());
    }

    /**
     * @return array
     */
    public function sheetMocksArrayProvider()
    {
        return $this->implodeDataProvider($this->sheetMockProvider());
    }

    /**
     * @return array
     */
    public function sheetMocksWithExistingTitleArrayProvider()
    {
        return $this->getDataProvidersCartesianProduct($this->sheetMocksArrayProvider(), $this->sheetTitleProvider());
    }

    /**
     * @return array
     */
    public function sheetMocksWithNonExistingTitleArrayProvider()
    {
        return $this->getDataProvidersCartesianProduct(
            $this->sheetMocksArrayProvider(),
            $this->nonExistingSheetTitleProvider()
        );
    }

    /**
     * @return array
     */
    public function rangeProvider()
    {
        return [
            ['A2:E'],
            ['A1:C1'],
            ['A1:D5'],
            ['B1:B100']
        ];
    }

    /**
     * @return array
     */
    public function rangeValuesProvider()
    {
        return $this->mapDataProviders(
            $this->getExpandRangeCallback(),
            $this->rangeProvider()
        );
    }

    /**
     * @return array
     */
    public function rangeAddressComponentsWithValuesProvider()
    {
        return $this->getDataProvidersCartesianProduct(
            $this->sheetTitleProvider(),
            $this->mergeDataProviders($this->rangeProvider(), $this->rangeValuesProvider())
        );
    }

    /**
     * @return array
     */
    public function sheetDataRowProvider()
    {
        $range = 'A1:A10';
        $values = call_user_func_array('array_merge', $this->expandRange($range));
        $cartesian = $this->getDataProvidersCartesianProduct(
            $this->sheetTitleProvider(),
            $this->mergeDataProviders([[$range]], [[$values]])
        );
        return $cartesian;
    }

    /**
     * @return array
     */
    public function sheetDataRowsArrayProvider()
    {
        return $this->getDataProvidersCartesianProduct($this->sheetTitleProvider(), $this->rangeValuesProvider());
    }
}
