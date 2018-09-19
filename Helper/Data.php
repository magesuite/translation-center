<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Component\ComponentRegistrar;

/**
 * General helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @param Context $context
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        Context $context,
        ComponentRegistrar $componentRegistrar
    ) {
        $this->componentRegistrar = $componentRegistrar;
        parent::__construct($context);
    }

    public function getLanguagePacksOptionsArray()
    {
        $languagePackOptions = [];
        $languagePacks = $this->componentRegistrar->getPaths(ComponentRegistrar::LANGUAGE);
        foreach ($languagePacks as $languagePackKey => $languagePackDir) {
            $languagePackOptions[] = ['value' => $languagePackDir, 'label' => $languagePackKey];
        }
        return $languagePackOptions;
    }
}
