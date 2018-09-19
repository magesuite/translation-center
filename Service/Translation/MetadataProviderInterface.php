<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

/**
 * Translations metadata provider interface
 */
interface MetadataProviderInterface
{
    /**
     * @param array $prependData
     * @return array
     */
    public function getMetadata(array $prependData = []);
}
