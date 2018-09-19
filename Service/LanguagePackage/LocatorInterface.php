<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\LanguagePackage;

/**
 * Language package locator interface
 */
interface LocatorInterface
{
    /**
     * @param string $locale
     * @param string $vendor
     * @return string|null
     */
    public function locateLanguagePackage($locale, $vendor);
}
