<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

class RequestFactory
{
    /**
     * @param string $requestType
     * @param array $params
     * @return \Google_Service_Sheets_Request
     */
    public function create($requestType, array $params)
    {
        return new \Google_Service_Sheets_Request([$requestType => $params]);
    }

    /**
     * @param array $requests
     * @return \Google_Service_Sheets_BatchUpdateSpreadsheetRequest
     */
    public function createBatchUpdateSpreadsheet(array $requests)
    {
        $requestObjects = [];
        foreach ($requests as $requestType => $params) {
            $requestObjects[] = $this->create($requestType, $params);
        }
        return new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest(
            ['requests' => $requestObjects]
        );
    }
}
