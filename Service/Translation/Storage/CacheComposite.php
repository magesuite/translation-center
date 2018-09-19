<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

use Braintree\Exception;
use MageSuite\TranslationCenter\Service\ConfigInterface;
use MageSuite\TranslationCenter\Service\Translation\StorageInterface;
use Magento\Framework\ObjectManagerInterface;

class CacheComposite implements CacheCompositeInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var null|string
     */
    protected $targetInstanceName;

    /**
     * @var null|string
     */
    protected $cacheInstanceName;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var bool
     */
    protected $isShared;

    /**
     * @var StorageInterface
     */
    protected $cacheStorage;

    /**
     * @var StorageInterface
     */
    protected $targetStorage;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $config
     * @param bool $shared
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $config,
        $shared = true
    ) {
        $this->objectManager = $objectManager;
        $this->targetInstanceName = $config->getStorageType();
        $this->cacheInstanceName = $config->getCacheStorageType();
        $this->locales = $config->getLocales();
        $this->isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['targetStorage', 'cacheStorage', 'locales', 'isShared', 'targetInstanceName', 'cacheInstanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function __wakeup()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->targetStorage = clone $this->getTargetStorage();
        $this->cacheStorage = clone $this->getCacheStorage();
    }

    /**
     * @return StorageInterface
     */
    protected function getCacheStorage()
    {
        if (!$this->cacheStorage) {
            $this->cacheStorage = true === $this->isShared
                ? $this->objectManager->get($this->cacheInstanceName ?: $this->targetInstanceName)
                : $this->objectManager->create($this->cacheInstanceName ?: $this->targetInstanceName);
        }
        return $this->cacheStorage;
    }

    /**
     * @return StorageInterface
     */
    protected function getTargetStorage()
    {
        if (!$this->targetStorage) {
            $this->targetStorage = true === $this->isShared
                ? $this->objectManager->get($this->targetInstanceName)
                : $this->objectManager->create($this->targetInstanceName);
        }
        return $this->targetStorage;
    }

    /**
     * @inheritdoc
     */
    public function fetch($localeCode)
    {
        return $this->getCacheStorage()->fetch($localeCode);
    }

    /**
     * @inheritdoc
     */
    public function add($textToTranslate, $translatedText, $localeCode, array $metadata)
    {
        $this->getCacheStorage()->add($textToTranslate, $translatedText, $localeCode, $metadata);
    }

    /**
     * @inheritdoc
     */
    public function setData(array $translationData, $localeCode)
    {
        $this->getCacheStorage()->setData($translationData, $localeCode);
    }

    /**
     * @inheritdoc
     */
    public function persist()
    {
        $this->getCacheStorage()->persist();
    }

    /**
     * @inheritdoc
     */
    public function clear($localeCode)
    {
        $this->getCacheStorage()->clear($localeCode);
    }

    /**
     * @inheritdoc
     */
    public function persistCache()
    {
        if ($this->cacheInstanceName && $this->cacheInstanceName != $this->targetInstanceName) {
            foreach ($this->locales as $locale) {
                $translationData = $this->fetch($locale);
                if (!empty($translationData)) {
                    $this->getTargetStorage()->setData($translationData, $locale);
                    $this->getTargetStorage()->persist();
                    $this->setData($this->getTargetStorage()->fetch($locale), $locale);
                }
            }
            $this->persist();
        }
    }
}
