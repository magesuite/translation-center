<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service;

/**
 * General configuration interface
 */
interface ConfigInterface
{
    /**
     * Check whether translation interceptor is enabled
     *
     * @param string|null $localeCode
     * @return bool
     */
    public function isInterceptorActive($localeCode = null);

    /**
     * Return locales the extension is configured to intercept for
     *
     * @return array
     */
    public function getLocales();

    /**
     * Return class name of the configured translation storage type
     *
     * @return string|null
     */
    public function getStorageType();

    /**
     * Check whether intercepted translations should be stored with metadata
     *
     * @return bool
     */
    public function isMetadataStoringActive();

    /**
     * Return vendor name for the newly generated language packages
     *
     * @return string
     */
    public function getLanguagePackageVendor();

    /**
     * Return vendor name, the newly generated language packages shall inherit from
     *
     * @return string|null
     */
    public function getLanguagePackageParentVendor();

    /**
     * Check whether caching of translation interceptions is enabled
     *
     * @return bool
     */
    public function isInterceptionCacheActive();

    /**
     * Return class name of the configured storage for interceptions caching
     *
     * @return string|null
     */
    public function getCacheStorageType();
}
