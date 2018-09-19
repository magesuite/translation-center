<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

class Aggregator implements AggregatorInterface
{

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Aggregator constructor
     *
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     */
    public function addSystemTranslation($textToTranslate, $translatedText, $localeCode, array $metadata = [])
    {
        $this->storage->add($textToTranslate, $translatedText, $localeCode, $metadata);
    }

    /**
     * @param string $localeCode
     * @return array
     */
    public function fetchSystemTranslations($localeCode)
    {
        return $this->storage->fetch($localeCode);
    }
}
