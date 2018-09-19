<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 - 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Model\System\Import\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * List of locales used in all store views
 */
class Locale implements ArrayInterface
{
    /**
     * XML path to "Store locale" config option
     */
    const XML_PATH_STORE_LOCALE_CODE = 'general/locale/code';

    /**
     * Store config scope literal
     */
    const CONFIG_SCOPE_STORES = 'stores';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var array|null
     */
    protected $options = null;

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * Import locale source model constructor
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @return mixed
     */
    protected function getCurrentLocale()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: [['value' => '<locale code>', 'label' => '<locale label>'], ...]
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options = [];
            foreach ($this->storeManager->getStores() as $store) {
                $locale = $this->scopeConfig->getValue(
                    self::XML_PATH_STORE_LOCALE_CODE,
                    self::CONFIG_SCOPE_STORES,
                    $store
                );
                $this->options[$locale] = [
                    'value' => $locale,
                    'label' => sprintf(
                        '%s (%s)',
                        ucwords(\Locale::getDisplayLanguage($locale, $this->getCurrentLocale())),
                        \Locale::getDisplayRegion($locale, $this->getCurrentLocale())
                    )
                ];
            }
        }
        return array_values($this->options);
    }
}
