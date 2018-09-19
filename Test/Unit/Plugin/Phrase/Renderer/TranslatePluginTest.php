<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Plugin\Phrase\Renderer;

use MageSuite\TranslationCenter\Plugin\Phrase\Renderer\TranslatePlugin;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TranslateRendererPluginTest extends TestCase
{
    /**
     * @var \Magento\Framework\Phrase\Renderer\Translate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translateRendererMock;

    /**
     * @var \MageSuite\TranslationCenter\Service\Translation\AggregatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationAggregatorMock;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeResolverMock;

    /**
     * @var \MageSuite\TranslationCenter\Service\Translation\MetadataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadataProviderMock;

    /**
     * @var TranslatePlugin
     */
    protected $pluginInstance;

    protected function setUp()
    {
        $this->translateRendererMock = $this->getMockBuilder(\Magento\Framework\Phrase\Renderer\Translate::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->translationAggregatorMock = $this->getMockBuilder(
            \MageSuite\TranslationCenter\Service\Translation\AggregatorInterface::class
        )
            ->getMock();

        $this->localeResolverMock = $this->getMockBuilder(\Magento\Framework\Locale\ResolverInterface::class)
            ->getMock();

        $this->metadataProviderMock = $this->getMockBuilder(
            \MageSuite\TranslationCenter\Service\Translation\MetadataProviderInterface::class
        )
            ->getMock();

        /** @var \MageSuite\TranslationCenter\Service\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject $configStub */
        $configStub = $this->getMockBuilder(\MageSuite\TranslationCenter\Service\ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $configStub->method('isInterceptorActive')
            ->willReturn(true);

        $configStub->method('isMetadataStoringActive')
            ->willReturn(true);

        /** @var \Psr\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject $loggerStub */
        $loggerStub = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->getMock();

        $this->pluginInstance = new TranslatePlugin(
            $this->translationAggregatorMock,
            $this->localeResolverMock,
            $this->metadataProviderMock,
            $configStub,
            $loggerStub
        );
    }

    /**
     * @param string $expectedTranslation
     * @return \Closure
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getProceedClosureStub($expectedTranslation)
    {
        return function (array $source, array $arguments) use ($expectedTranslation) {
            return $expectedTranslation;
        };
    }

    /**
     * @param array $source
     * @param array $arguments
     * @param string $expectedTranslation
     * @param string $localeCode
     * @param array $metadata
     * @return string
     */
    protected function prepareMocksAndCallAroundRenderPluginMethod(
        $source,
        array $arguments,
        $expectedTranslation,
        $localeCode,
        array $metadata
    ) {
        $this->localeResolverMock
            ->method('getLocale')
            ->willReturn($localeCode);

        $this->metadataProviderMock
            ->method('getMetadata')
            ->willReturn($metadata);

        return $this->pluginInstance->aroundRender(
            $this->translateRendererMock,
            $this->getProceedClosureStub($expectedTranslation),
            $source,
            $arguments
        );
    }

    public function testPluginCanBeInstantiated()
    {
        $this->assertInstanceOf(TranslatePlugin::class, $this->pluginInstance);
    }

    /**
     * @param $textToTranslate
     * @param $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testAroundRenderMethodReturnsResultOfProvidedProceedClosure(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->assertSame(
            $translatedText,
            $this->prepareMocksAndCallAroundRenderPluginMethod(
                [$textToTranslate],
                [],
                $translatedText,
                $localeCode,
                $metadata
            ),
            'aroundRender() method does not return expected result!'
        );
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testInterceptedTranslationIsSubmittedToTranslationAggregator(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->translationAggregatorMock
            ->expects($this->once())
            ->method('addSystemTranslation')
            ->with(
                $this->equalTo($textToTranslate),
                $this->equalTo($translatedText),
                $this->equalTo($localeCode),
                $this->equalTo($metadata)
            );

        $this->prepareMocksAndCallAroundRenderPluginMethod(
            [$textToTranslate],
            [],
            $translatedText,
            $localeCode,
            $metadata
        );
    }
}
