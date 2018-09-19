<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Plugin\Phrase\Renderer;

use MageSuite\TranslationCenter\Service\ConfigInterface;
use MageSuite\TranslationCenter\Service\Translation\AggregatorInterface;
use MageSuite\TranslationCenter\Service\Translation\MetadataProviderInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Locale\ResolverInterface as LocaleResolverInterface;
use Magento\Framework\Phrase\Renderer\Translate as TranslateRenderer;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TranslatePlugin
{
    /**
     * @var AggregatorInterface
     */
    protected $translationAggregator;

    /**
     * @var LocaleResolverInterface
     */
    protected $localeResolver;

    /**
     * @var MetadataProviderInterface
     */
    protected $metadataProvider;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        AggregatorInterface $translationAggregator,
        LocaleResolverInterface $localeResolver,
        MetadataProviderInterface $metadataProvider,
        ConfigInterface $config,
        LoggerInterface $logger
    ) {
        $this->translationAggregator = $translationAggregator;
        $this->localeResolver = $localeResolver;
        $this->metadataProvider = $metadataProvider;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param TranslateRenderer $subject
     * @param \Closure $proceed
     * @param array $source
     * @param array $arguments
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRender(
        TranslateRenderer $subject,
        \Closure $proceed,
        array $source,
        array $arguments
    ) {
        $translatedText = $proceed($source, $arguments);
        $locale = $this->localeResolver->getLocale();

        if ($this->config->isInterceptorActive($locale)) {
            try {
                $textToTranslate = end($source);
                $this->translationAggregator->addSystemTranslation(
                    $textToTranslate,
                    $translatedText,
                    $locale,
                    $this->config->isMetadataStoringActive()
                        ? $this->metadataProvider->getMetadata()
                        : []
                );
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $translatedText;
    }
}
