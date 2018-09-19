<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation;

use MageSuite\TranslationCenter\Service\Translation\MetadataProvider;
use MageSuite\TranslationCenter\Service\Translation\MetadataProviderInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\UrlInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class MetadataProviderTest extends TestCase
{
    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlResolverMock;

    /**
     * @var MetadataProvider
     */
    protected $metadataProviderInstance;

    protected function setUp()
    {
        $this->urlResolverMock = $this->getMockBuilder(UrlInterface::class)
            ->getMock();

        $this->metadataProviderInstance = new MetadataProvider($this->urlResolverMock);
    }

    /**
     * @param string $url
     */
    protected function prepareUrlResolverMock($url)
    {
        $this->urlResolverMock
            ->method('getCurrentUrl')
            ->willReturn($url);
    }

    public function testMetadataProviderCanBeInstantiated()
    {
        $this->assertInstanceOf(MetadataProvider::class, $this->metadataProviderInstance);
    }

    public function testMetadataProviderImplementsMetadataProviderInterface()
    {
        $this->assertInstanceOf(MetadataProviderInterface::class, $this->metadataProviderInstance);
    }

    /**
     * @param array $prependMetadata
     * @param array $urlMetadata
     *
     * @dataProvider translationMetadataProvider
     */
    public function testGetMetadataMethodReturnsMetadataArray(array $prependMetadata, array $urlMetadata)
    {
        $this->prepareUrlResolverMock(isset($urlMetadata['url']) ? $urlMetadata['url'] : '');
        $this->assertSame(
            array_merge($prependMetadata, $urlMetadata),
            $this->metadataProviderInstance->getMetadata($prependMetadata)
        );
    }
}
