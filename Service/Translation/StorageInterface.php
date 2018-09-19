<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

/**
 * Translations storage interface
 */
interface StorageInterface
{
    /**
     * Fetch translation data array for given locale
     *
     * @param string $localeCode
     * @return array
     */
    public function fetch($localeCode);

    /**
     * Add single row to the translation data array
     *
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     * @return void
     */
    public function add($textToTranslate, $translatedText, $localeCode, array $metadata);

    /**
     * Set translation data array, overwrite if exists
     *
     * @param array $translationData
     * @param string $localeCode
     * @return void
     */
    public function setData(array $translationData, $localeCode);

    /**
     * Persist translation data array to the storage backend
     *
     * @return void
     */
    public function persist();

    /**
     * Clears storage out of all data
     *
     * @param string $localeCode
     * @return void
     */
    public function clear($localeCode);
}
