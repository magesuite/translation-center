<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\ValueRangeFactory;
use MageSuite\TranslationCenter\Test\TestCase;

class ValueRangeFactoryTest extends TestCase
{
    /**
     * @var ValueRangeFactory
     */
    protected $factoryInstance;

    protected function setUp()
    {
        $this->factoryInstance = new ValueRangeFactory();
    }

    public function testValueRangeFactoryCanBeInstantiated()
    {
        $this->assertInstanceOf(ValueRangeFactory::class, $this->factoryInstance);
    }

    /**
     * @param array $dataRows
     *
     * @dataProvider dataMatrixProvider
     */
    public function testCreateMethodReturnsValueRangeInstance(array $dataRows)
    {
        $valueRangeInstance = $this->factoryInstance->create($dataRows);
        $this->assertInstanceOf(\Google_Service_Sheets_ValueRange::class, $valueRangeInstance);
    }

    /**
     * @return array
     */
    public function dataMatrixProvider()
    {
        return [
            [[[1, 2], [3,4]]],
            [[[10, 20], [30, 40], [50, 60]]],
            [[['Lorem ipsum', 'dolor sit amet', 'consectetur adipiscing elit']]]
        ];
    }
}
