<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

class Facade
{
    /**
     * @var \Google_Service_Sheets
     */
    protected $googleService;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var ValueRangeFactory
     */
    protected $valueRangeFactory;

    /**
     * @var string|null
     */
    protected $spreadsheetId = null;

    /**
     * @var array|null
     */
    protected $sheetsCache = null;

    /**
     * @var array
     */
    protected $valuesCache = array();

    /**
     * Google Sheets Facade constructor
     *
     * @param \Google_Service_Sheets $googleSheetsService
     * @param Client $googleClientAdapter
     * @param ConfigInterface $googleSheetsConfig
     * @param RequestFactory $googleSheetsRequestFactory
     * @param ValueRangeFactory $googleSheetsValueRangeFactory
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        \Google_Service_Sheets $googleSheetsService,
        Client $googleClientAdapter,
        ConfigInterface $googleSheetsConfig,
        RequestFactory $googleSheetsRequestFactory,
        ValueRangeFactory $googleSheetsValueRangeFactory
    ) {
        $this->googleService = $googleSheetsService;
        $this->spreadsheetId = $googleSheetsConfig->getSpreadsheetId();
        $this->requestFactory = $googleSheetsRequestFactory;
        $this->valueRangeFactory = $googleSheetsValueRangeFactory;
    }

    protected function flushValuesCache()
    {
        $this->valuesCache = [];
    }

    protected function flushSheetsCache()
    {
        $this->sheetsCache = null;
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
     * @return array|mixed
     */
    public function getSheets()
    {
        if (null === $this->sheetsCache) {
            $this->sheetsCache = $this->googleService->spreadsheets->get($this->spreadsheetId)->getSheets();
        }
        return $this->sheetsCache;
    }

    /**
     * @param string $sheetTitle
     * @return mixed|null
     */
    public function getSheetByTitle($sheetTitle)
    {
        foreach ($this->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() == $sheetTitle) {
                return $sheet;
            }
        }
        return null;
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @return array|mixed
     */
    public function getValues($sheetTitle, $range)
    {
        $fullRangeAddress = $this->getFullRangeAddress($sheetTitle, $range);
        if (!array_key_exists($fullRangeAddress, $this->valuesCache)) {
            $values = $this->googleService->spreadsheets_values->get(
                $this->spreadsheetId,
                $fullRangeAddress
            );
            $values = $values->getValues();
            $this->valuesCache[$fullRangeAddress] = null !== $values ? $values : [];
        }
        return $this->valuesCache[$fullRangeAddress];
    }

    /**
     * @param string $sheetTitle
     * @return bool
     */
    public function sheetExists($sheetTitle)
    {
        if ($this->getSheetByTitle($sheetTitle)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $sheetTitle
     * @return \Google_Service_Sheets_BatchUpdateSpreadsheetResponse
     */
    public function createSheet($sheetTitle)
    {
        $request = $this->requestFactory->createBatchUpdateSpreadsheet([
            'addSheet' => ['properties' => ['title' => $sheetTitle]]
        ]);

        /** @var \Google_Service_Sheets_BatchUpdateSpreadsheetResponse $response */
        $response = $this->googleService->spreadsheets->batchUpdate($this->spreadsheetId, $request);

        $this->flushSheetsCache();

        return $response;
    }

    /**
     * @param $sheetTitle
     * @param $range
     * @param array $dataRows
     * @return \Google_Service_Sheets_UpdateValuesResponse
     */
    public function updateRows($sheetTitle, $range, array $dataRows)
    {
        $fullRangeAddress = $this->getFullRangeAddress($sheetTitle, $range);
        $valueRange = $this->valueRangeFactory->create($dataRows, $fullRangeAddress);

        /** @var \Google_Service_Sheets_UpdateValuesResponse $response */
        $response = $this->googleService->spreadsheets_values->update(
            $this->spreadsheetId,
            $fullRangeAddress,
            $valueRange,
            ['valueInputOption' => 'RAW']
        );

        $this->flushValuesCache();

        return $response;
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRow
     * @return \Google_Service_Sheets_AppendValuesResponse
     */
    public function appendRow($sheetTitle, $range, array $dataRow)
    {
        return $this->appendRows($sheetTitle, $range, [$dataRow]);
    }

    /**
     * @param string $sheetTitle
     * @param string $range
     * @param array $dataRows
     * @return \Google_Service_Sheets_AppendValuesResponse
     */
    public function appendRows($sheetTitle, $range, array $dataRows)
    {
        $fullRangeAddress = $this->getFullRangeAddress($sheetTitle, $range);
        $valueRange = $this->valueRangeFactory->create($dataRows, $fullRangeAddress);

        /** @var \Google_Service_Sheets_AppendValuesResponse $response */
        $response = $this->googleService->spreadsheets_values->append(
            $this->spreadsheetId,
            $fullRangeAddress,
            $valueRange,
            ['valueInputOption' => 'RAW']
        );

        $this->flushValuesCache();

        return $response;
    }

    /**
     * @param string $searchedString
     * @param string $sheetTitle
     * @param string $range
     *
     * @return array|null
     */
    public function findRow($searchedString, $sheetTitle, $range)
    {
        $values = $this->getValues($sheetTitle, $range);
        if (!empty($values)) {
            foreach (array_keys($values[0]) as $columnKey) {
                $foundRowIndex = array_search($searchedString, array_column($values, $columnKey));
                if (false !== $foundRowIndex) {
                    return $values[$foundRowIndex];
                }
            }
        }
        return null;
    }
}
