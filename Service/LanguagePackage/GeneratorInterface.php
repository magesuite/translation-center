<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 - 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\LanguagePackage;

/**
 * Custom language package generator interface
 */
interface GeneratorInterface
{
    /**
     * @param string $locale
     * @param string $vendor
     * @param string|null $parentVendor
     * @return string
     */
    public function generateLanguagePackage($locale, $vendor, $parentVendor = null);
}
