<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\StorageInterface;

abstract class AbstractStorage implements StorageInterface
{
    /**
     * @var array
     */
    protected $storageData = array();

    /**
     * @param string $localeCode
     */
    protected function initStorageData($localeCode)
    {
        if (!array_key_exists($localeCode, $this->storageData)) {
            $this->storageData[$localeCode] = [];
        }
    }

    /**
     * @inheritdoc
     */
    public function add($textToTranslate, $translatedText, $localeCode, array $metadata)
    {
        $this->initStorageData($localeCode);
        $translationData = array_merge([$textToTranslate, $translatedText], $metadata);
        if (false === array_search($translationData, $this->storageData[$localeCode])) {
            $this->storageData[$localeCode][] = $translationData;
        }
    }

    /**
     * @inheritdoc
     */
    public function setData(array $translationData, $localeCode)
    {
        $this->initStorageData($localeCode);
        $this->storageData[$localeCode] = $translationData;
    }
}
