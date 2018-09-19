<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Helper;

use MageSuite\TranslationCenter\Helper\Data;
use MageSuite\TranslationCenter\Test\TestCase;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DataTest extends TestCase
{
    /**
     * @var Data
     */
    protected $helperInstance;

    protected function getLanguagePacksDummyData()
    {
        return [
            'creativestyle_de_de' => '/tmp',
            'creativestyle_pl_pl' => '/tmp',
            'creativestyle_en_us' => '/tmp'
        ];
    }

    protected function setUp()
    {
        /** @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject $contextStub */
        $contextStub = $this->getMockBuilder(\Magento\Framework\App\Helper\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\Component\ComponentRegistrar|\PHPUnit_Framework_MockObject_MockObject $componentRegistrarStub */
        $componentRegistrarStub = $this->getMockBuilder(\Magento\Framework\Component\ComponentRegistrar::class)
            ->disableOriginalConstructor()
            ->getMock();

        $componentRegistrarStub->method('getPaths')
            ->with($this->equalTo(\Magento\Framework\Component\ComponentRegistrar::LANGUAGE))
            ->willReturn($this->getLanguagePacksDummyData());

        $this->helperInstance = new Data($contextStub, $componentRegistrarStub);
    }

    public function testHelperCanBeInstantiated()
    {
        $this->assertInstanceOf(Data::class, $this->helperInstance);
    }

    public function testGetLanguagePacksOptionsArrayReturnsOptionsArray()
    {
        $expectedResult = array_map(
            function ($key, $value) {
                return ['value' => $value, 'label' => $key];
            },
            array_keys($this->getLanguagePacksDummyData()),
            array_values($this->getLanguagePacksDummyData())
        );
        $this->assertSame($expectedResult, $this->helperInstance->getLanguagePacksOptionsArray());
    }
}
