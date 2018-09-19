<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Facade as GoogleSheetsFacade;

class GoogleSheets extends AbstractStorage
{
    /**
     * @var GoogleSheetsFacade
     */
    protected $googleSheetsFacade;

    /**
     * @var SynchroniseStrategyInterface
     */
    protected $synchroniseStrategy;

    /**
     * @param GoogleSheetsFacade $googleSheetsFacade
     * @param SynchroniseStrategyInterface $synchroniseStrategy
     */
    public function __construct(
        GoogleSheetsFacade $googleSheetsFacade,
        SynchroniseStrategyInterface $synchroniseStrategy
    ) {
        $this->googleSheetsFacade = $googleSheetsFacade;
        $this->synchroniseStrategy = $synchroniseStrategy;
    }

    /**
     * @param string $localeCode
     */
    protected function initSheetForLocale($localeCode)
    {
        if (!$this->googleSheetsFacade->sheetExists($localeCode)) {
            $this->googleSheetsFacade->createSheet($localeCode);
        }
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getSheetRange(array $data)
    {
        $columnCount = max(array_map('count', $data));
        return sprintf('A1:%s', $columnCount ? chr(ord('A') + $columnCount - 1) : 'A1');
    }

    /**
     * @inheritdoc
     */
    public function persist()
    {
        foreach ($this->storageData as $localeCode => $translationData) {
            $this->initSheetForLocale($localeCode);
            $sheetRange = $this->getSheetRange($translationData);
            $this->googleSheetsFacade->updateRows(
                $localeCode,
                $sheetRange,
                $this->synchroniseStrategy->synchronise(
                    $translationData,
                    $this->googleSheetsFacade->getValues($localeCode, $sheetRange)
                )
            );
        }
        $this->storageData = [];
    }

    /**
     * @param $localeCode
     * @return array
     */
    public function fetch($localeCode)
    {
        return $this->googleSheetsFacade->getValues($localeCode, 'A1:B');
    }

    /**
     * Clears storage out of all data
     *
     * @param string $localeCode
     * @return void
     */
    public function clear($localeCode)
    {
        $sheetRange = $this->getSheetRange($this->fetch($localeCode));
        $this->googleSheetsFacade->updateRows($localeCode, $sheetRange, []);
    }
}
