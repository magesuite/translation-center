<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

/**
 * Translations importer interface
 */
interface ImporterInterface
{
    /**
     * @param array $data
     * @param string $targetPath
     * @return void
     */
    public function import(array $data, $targetPath);
}
