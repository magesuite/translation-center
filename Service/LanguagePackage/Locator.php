<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\LanguagePackage;

use Magento\Framework\Component\ComponentRegistrar;

class Locator implements LocatorInterface
{
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        ComponentRegistrar $componentRegistrar
    ) {
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * @param string $locale
     * @param string $vendor
     * @return string|null
     */
    public function locateLanguagePackage($locale, $vendor)
    {
        $path = $this->componentRegistrar->getPath(
            ComponentRegistrar::LANGUAGE,
            sprintf('%s_%s', $vendor, strtolower($locale))
        );
        return $path;
    }
}
