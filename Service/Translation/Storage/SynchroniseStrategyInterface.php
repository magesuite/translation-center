<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

interface SynchroniseStrategyInterface
{
    /**
     * @param array $localData
     * @param array $remoteData
     * @return array
     */
    public function synchronise(array $localData, array $remoteData);
}
