<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv as CsvFile;

class Csv extends AbstractStorage
{
    /**
     * Target mode for credentials directory
     */
    const TARGET_DIRECTORY_MODE = 0750;

    /**
     * @var CsvFile
     */
    protected $csvProcessor;

    /**
     * @var SynchroniseStrategyInterface
     */
    protected $synchroniseStrategy;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var string
     */
    protected $targetDir;

    /**
     * @param CsvFile $csvProcessor
     * @param SynchroniseStrategyInterface $synchroniseStrategy
     * @param DirectoryList $directoryList
     * @param string|null $targetDir
     */
    public function __construct(
        CsvFile $csvProcessor,
        SynchroniseStrategyInterface $synchroniseStrategy,
        DirectoryList $directoryList,
        $targetDir = null
    ) {
        $this->csvProcessor = $csvProcessor;
        $this->synchroniseStrategy = $synchroniseStrategy;
        $this->directoryList = $directoryList;
        $this->targetDir = $targetDir
            ?: $this->directoryList->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . 'translation-center';
        if (!file_exists($this->targetDir)) {
            mkdir($this->targetDir, self::TARGET_DIRECTORY_MODE, true);
        }
    }

    /**
     * @param $localeCode
     * @return string
     */
    protected function getTargetPath($localeCode)
    {
        return $this->targetDir . DIRECTORY_SEPARATOR . $localeCode . '.csv';
    }

    /**
     * @inheritdoc
     */
    public function fetch($localeCode)
    {
        $csvPath = $this->getTargetPath($localeCode);
        if (file_exists($csvPath)) {
            return $this->csvProcessor->getData($csvPath);
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function persist()
    {
        foreach ($this->storageData as $localeCode => $translationData) {
            $this->csvProcessor->saveData(
                $this->getTargetPath($localeCode),
                $this->synchroniseStrategy->synchronise(
                    $translationData,
                    $this->fetch($localeCode)
                )
            );
        }
        $this->storageData = [];
    }

    /**
     * @inheritdoc
     */
    public function clear($localeCode)
    {
        $csvPath = $this->getTargetPath($localeCode);
        if (file_exists($csvPath)) {
            $this->csvProcessor->saveData($csvPath, []);
        }
    }
}
