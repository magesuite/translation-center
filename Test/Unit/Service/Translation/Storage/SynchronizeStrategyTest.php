<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\SynchroniseStrategy;
use MageSuite\TranslationCenter\Service\Translation\Storage\SynchroniseStrategyInterface;
use MageSuite\TranslationCenter\Test\TestCase;

class SynchronizeStrategyTest extends TestCase
{
    /**
     * @var SynchroniseStrategy
     */
    protected $strategyInstance;

    protected function setUp()
    {
        $this->strategyInstance = new SynchroniseStrategy();
    }

    public function testSynchronizeStrategyCanBeInstantiated()
    {
        $this->assertInstanceOf(SynchroniseStrategy::class, $this->strategyInstance);
    }

    public function testSynchronizeStrategyImplementsSynchronizeStrategyInterface()
    {
        $this->assertInstanceOf(SynchroniseStrategyInterface::class, $this->strategyInstance);
    }
    /**
     * @param array $localData
     * @param array $remoteData
     * @param array $synchronizedData
     *
     * @dataProvider synchroniseDataProvider
     */
    public function testSynchroniseMethodReturnsCorrectlySynchronizedDataArray(
        array $localData,
        array $remoteData,
        array $synchronizedData
    ) {
        $this->assertSame(
            $synchronizedData,
            $this->strategyInstance->synchronise($localData, $remoteData)
        );
    }

    public function synchroniseDataProvider()
    {
        return [
            [
                [['A1', 'A1', 'local meta'], ['A2', 'A2', 'local meta'], ['A3', 'A3']],
                [['A1', 'A1T', 'local meta'], ['A2', 'A2T'], ['A3', 'A3T', 'remote meta']],
                [['A1', 'A1T', 'local meta'], ['A2', 'A2T', 'local meta'], ['A3', 'A3T', 'remote meta']]
            ],
            [
                [['A1', 'A1', 'local meta'], ['A2', 'A2T'], ['A3', 'A3']],
                [['A1', 'A1', 'local meta'], ['A2', 'A2', 'remote meta'], ['A3', 'A3T']],
                [['A1', 'A1', 'local meta'], ['A2', 'A2T', 'remote meta'], ['A3', 'A3T']]
            ],
            [
                [['A1', 'A1', 'local meta']],
                [['A1', 'A1T', 'remote meta'], ['A2', 'A2', 'remote meta'], ['A3', 'A3']],
                [['A1', 'A1T', "remote meta\nlocal meta"], ['A2', 'A2', 'remote meta'], ['A3', 'A3']]
            ],
            [
                [['A2', 'A2T']],
                [['A1', 'A1T', 'remote meta'], ['A3', 'A3', 'remote meta'], ['A4', 'A4']],
                [['A1', 'A1T', 'remote meta'], ['A2', 'A2T'], ['A3', 'A3', 'remote meta'], ['A4', 'A4']]
            ]
        ];
    }
}
