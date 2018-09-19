<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

use Magento\Framework\File\Csv;

class Importer implements ImporterInterface
{
    /**
     * @var Csv
     */
    protected $csvProcessor;

    public function __construct(Csv $csvProcessor)
    {
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * @param array $row
     * @return bool
     */
    protected function validateRow(array $row)
    {
        return isset($row[0]) && isset($row[1]) && $row[1] !== $row[0];
    }

    /**
     * @param array $row
     * @return bool
     */
    protected function sanitizeRow(array $row)
    {
        return array_slice($row, 0, 2);
    }

    /**
     * @param array $data
     * @param string $targetPath
     * @return void
     */
    public function import(array $data, $targetPath)
    {
        $dataToSave = [];
        foreach ($data as $row) {
            if ($this->validateRow($row)) {
                $dataToSave[] = $this->sanitizeRow($row);
            }
        }
        $this->csvProcessor->saveData($targetPath, $dataToSave);
    }
}
