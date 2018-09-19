<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Plugin\Phrase\Renderer;

use MageSuite\TranslationCenter\Plugin\Phrase\Renderer\TranslatePlugin;
use MageSuite\TranslationCenter\Service\Translation\AggregatorInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Phrase\Renderer\Translate as TranslateRenderer;
use Magento\TestFramework\Interception\PluginList;
use Magento\TestFramework\ObjectManager;

class TranslatePluginTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $pluginName = 'creativestyle_translation_center_renderer_translate';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * @return array
     */
    protected function getTranslateRendererPluginInfo()
    {
        /** @var PluginList $pluginList */
        $pluginList = $this->objectManager->create(PluginList::class);
        return $pluginList->get(TranslateRenderer::class, []);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testTranslateRendererPluginIsConfiguredToInterceptCalls()
    {
        $pluginInfo = $this->getTranslateRendererPluginInfo();
        $this->assertSame(
            TranslatePlugin::class,
            $pluginInfo[$this->pluginName]['instance']
        );
    }

    /**
     * @param $textToTranslate
     *
     * @magentoAppArea frontend
     * @dataProvider translationPairProvider
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testTranslationIsSubmittedToAggregatorWhenPhraseIsTranslated($textToTranslate)
    {
        $this->markTestIncomplete();
        $localeResolver = $this->objectManager->create(ResolverInterface::class);

        $translationAggregatorMock = $this->getMock(AggregatorInterface::class);
        $translationAggregatorMock->expects($this->once())
            ->method('addSystemTranslation')
            ->with($this->equalTo($textToTranslate), $this->anything(), $this->equalTo($localeResolver->getLocale()));

        $this->objectManager->configure([
            'preferences' => [AggregatorInterface::class => AggregatorInterface::class],
            AggregatorInterface::class => ['shared' => true]
        ]);
        $this->objectManager->addSharedInstance($translationAggregatorMock, AggregatorInterface::class);

        Phrase::setRenderer($this->objectManager->get('Magento\Framework\Phrase\RendererInterface'));
        $phraseInstance = new Phrase($textToTranslate);
        $phraseInstance->render();
    }
}
