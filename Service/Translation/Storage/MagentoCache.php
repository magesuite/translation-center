<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

use Magento\Framework\App\CacheInterface;

class MagentoCache extends AbstractStorage
{
    const LOCALE_CACHE_KEY = 'TRANSLATION_CENTER_%s';

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var SynchroniseStrategyInterface
     */
    protected $synchroniseStrategy;

    /**
     * @param CacheInterface $cache
     * @param SynchroniseStrategyInterface $synchroniseStrategy
     */
    public function __construct(
        CacheInterface $cache,
        SynchroniseStrategyInterface $synchroniseStrategy
    ) {
        $this->cache = $cache;
        $this->synchroniseStrategy = $synchroniseStrategy;
    }

    /**
     * @param string $localeCode
     * @return string
     */
    protected function getLocaleCacheKey($localeCode)
    {
        return sprintf(self::LOCALE_CACHE_KEY, strtoupper($localeCode));
    }

    /**
     * @inheritdoc
     */
    public function fetch($localeCode)
    {
        return json_decode($this->cache->load($this->getLocaleCacheKey($localeCode))) ?: [];
    }

    /**
     * @inheritdoc
     */
    public function persist()
    {
        foreach ($this->storageData as $localeCode => $translationData) {
            $synchronizedData = $this->synchroniseStrategy->synchronise(
                $translationData,
                $this->fetch($localeCode)
            );
            $this->cache->save(
                json_encode($synchronizedData),
                $this->getLocaleCacheKey($localeCode)
            );
        }
        $this->storageData = [];
    }

    /**
     * @inheritdoc
     */
    public function clear($localeCode)
    {
        $this->cache->remove($this->getLocaleCacheKey($localeCode));
    }
}
