<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * General configuration
 */
class Config implements ConfigInterface
{
    /**
     * XML path to "Enable translation interceptor" config option
     */
    const XML_PATH_INTERCEPTOR_ACTIVE = 'translation_center/general/interceptor_active';

    /**
     * XML path to "Locales to intercept" config option
     */
    const XML_PATH_LOCALES = 'translation_center/general/locales';

    /**
     * XML path to "Translations storage" config option
     */
    const XML_PATH_STORAGE_TYPE = 'translation_center/general/storage_type';

    /**
     * XML path to "Store metadata" config option
     */
    const XML_PATH_METADATA_ACTIVE = 'translation_center/general/store_metadata';

    /**
     * XML path to "Enable interceptions caching" config option
     */
    const XML_PATH_CACHE_ACTIVE = 'translation_center/general/cache_active';

    /**
     * XML path to "Cache storage" config option
     */
    const XML_PATH_CACHE_STORAGE_TYPE = 'translation_center/general/cache_storage_type';

    /**
     * XML path to "Language package vendor" config option
     */
    const XML_PATH_LANGUAGE_PACKAGE_VENDOR = 'translation_center/general/language_package_vendor';

    /**
     * XML path to "Inherit language packages from" config option
     */
    const XML_PATH_LANGUAGE_PACKAGE_PARENT_VECTOR = 'translation_center/general/language_package_parent_vendor';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * General configuration constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isInterceptorActive($localeCode = null)
    {
        if ($this->scopeConfig->isSetFlag(self::XML_PATH_INTERCEPTOR_ACTIVE)) {
            if (null !== $localeCode) {
                return in_array($localeCode, $this->getLocales());
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getLocales()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LOCALES)
            ? explode(',', $this->scopeConfig->getValue(self::XML_PATH_LOCALES))
            : [];
    }

    /**
     * @inheritdoc
     */
    public function getStorageType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STORAGE_TYPE) ?: null;
    }

    /**
     * @inheritdoc
     */
    public function isMetadataStoringActive()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_METADATA_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function getLanguagePackageVendor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LANGUAGE_PACKAGE_VENDOR);
    }

    /**
     * @inheritdoc
     */
    public function getLanguagePackageParentVendor()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LANGUAGE_PACKAGE_PARENT_VECTOR) ?: null;
    }

    /**
     * @inheritdoc
     */
    public function isInterceptionCacheActive()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CACHE_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function getCacheStorageType()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CACHE_STORAGE_TYPE) ?: null;
    }
}
