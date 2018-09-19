<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Cron;

use MageSuite\TranslationCenter\Service\ConfigInterface;
use MageSuite\TranslationCenter\Service\Translation\Storage\CacheCompositeInterface;

class FlushInterceptionCache
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var CacheCompositeInterface
     */
    protected $storage;

    /**
     * @param ConfigInterface $config
     * @param CacheCompositeInterface $storage
     */
    public function __construct(
        ConfigInterface $config,
        CacheCompositeInterface $storage
    ) {
        $this->config = $config;
        $this->storage = $storage;
    }

    public function execute()
    {
        if ($this->config->isInterceptionCacheActive()) {
            $this->storage->persistCache();
        }
    }
}
