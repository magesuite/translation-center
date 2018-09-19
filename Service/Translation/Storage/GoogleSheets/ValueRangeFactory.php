<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

class ValueRangeFactory
{
    /**
     * @param array $data
     * @return array
     */
    protected function jsonEscape(array $data)
    {
        return json_decode(json_encode($data));
    }

    /**
     * @param array $dataRows
     * @param string|null $range
     * @return \Google_Service_Sheets_ValueRange
     */
    public function create(array $dataRows, $range = null)
    {
        return new \Google_Service_Sheets_ValueRange([
            'range' => $range,
            'values'=> $this->jsonEscape($dataRows)
        ]);
    }
}
