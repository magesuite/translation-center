<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Model\System\Config\Source\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\SpreadsheetsListInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * List of available spreadsheets
 */
class Spreadsheets implements ArrayInterface
{
    /**
     * @var SpreadsheetsListInterface
     */
    private $spreadsheetsList;

    /**
     * Spreadsheets list constructor
     *
     * @param SpreadsheetsListInterface $spreadsheetsList
     */
    public function __construct(SpreadsheetsListInterface $spreadsheetsList)
    {
        $this->spreadsheetsList = $spreadsheetsList;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: [['value' => '<spreadsheet ID>', 'label' => '<spreadsheet title>'], ...]
     */
    public function toOptionArray()
    {
        $options = [];
        try {
            $spreadsheets = $this->spreadsheetsList->getSpreadsheets();
            foreach ($spreadsheets as $spreadsheet) {
                $options[] = [
                    'value' => $spreadsheet->id,
                    'label' => $spreadsheet->name
                ];
            }
        } catch (\Google_Exception $e) {
            $options = [[
                'value' => '',
                'label' => __('-- Link with your Google Account first --')
            ]];
        }
        return $options;
    }
}
