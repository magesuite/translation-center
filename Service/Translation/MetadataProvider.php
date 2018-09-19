<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation;

use Magento\Framework\UrlInterface;

class MetadataProvider implements MetadataProviderInterface
{
    /**
     * String constant for the URL metadata when app runs in CLI
     */
    const CLI_ORIGIN = 'CLI / JavaScript';

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * MetadataProvider constructor
     *
     * @param UrlInterface $urlInterface
     */
    public function __construct(UrlInterface $urlInterface)
    {
        $this->urlInterface = $urlInterface;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function validateUrl($url)
    {
        $urlScheme = parse_url($url, PHP_URL_SCHEME);
        $urlHost = parse_url($url, PHP_URL_HOST);
        $urlPath = parse_url($url, PHP_URL_PATH);
        if ($urlScheme && $urlHost && $urlPath) {
            return true;
        }
        return false;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function sanitizeUrl($url)
    {
        $urlScheme = parse_url($url, PHP_URL_SCHEME);
        $urlHost = parse_url($url, PHP_URL_HOST);
        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlPort = parse_url($url, PHP_URL_PORT);
        return sprintf('%s://%s%s%s', $urlScheme, $urlHost, $urlPort ? ':' . $urlPort : '', $urlPath);
    }

    /**
     * @return string
     */
    protected function getCurrentUrl()
    {
        $url = $this->urlInterface->getCurrentUrl();
        if ($this->validateUrl($url)) {
            return $this->sanitizeUrl($url);
        }
        return self::CLI_ORIGIN;
    }

    /**
     * @param array $prependData
     * @return array
     */
    public function getMetadata(array $prependData = [])
    {
        return array_merge($prependData, ['url' => $this->getCurrentUrl()]);
    }
}
