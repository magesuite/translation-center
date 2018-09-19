<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

/**
 * Translations aggregator interface
 */
interface AggregatorInterface
{
    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     * @return void
     */
    public function addSystemTranslation($textToTranslate, $translatedText, $localeCode, array $metadata = []);

    /**
     * @param string $localeCode
     * @return array
     */
    public function fetchSystemTranslations($localeCode);
}
