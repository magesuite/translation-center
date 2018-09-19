<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Config as GoogleSheetsConfig;
use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ConfigInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface as ResourceConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigTest extends TestCase
{
    /**
     * @var GoogleSheetsConfig
     */
    protected $configInstance;

    protected function setUp()
    {
        /** @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject $scopeConfigStub */
        $scopeConfigStub = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $scopeConfigStub->method('getValue')
            ->willReturn('SOME_CONFIG_VALUE');

        $resourceConfigStub = $this->getMockBuilder(ResourceConfigInterface::class)
            ->getMock();

        $this->configInstance = new GoogleSheetsConfig($scopeConfigStub, $resourceConfigStub);
    }

    public function testGoogleSheetsConfigCanBeInstantiated()
    {
        $this->assertInstanceOf(GoogleSheetsConfig::class, $this->configInstance);
    }

    public function testGoogleSheetsConfigImplementsConfigInterface()
    {
        $this->assertInstanceOf(ConfigInterface::class, $this->configInstance);
    }

    public function testGetSpreadsheetIdMethodReturnsString()
    {
        $this->assertInternalType('string', $this->configInstance->getSpreadsheetId());
    }

    public function testGetApplicationNameMethodReturnsString()
    {
        $this->assertInternalType('string', $this->configInstance->getApplicationName());
    }

    public function testGetScopesMethodReturnsArray()
    {
        $this->assertInternalType('array', $this->configInstance->getScopes());
    }
}
