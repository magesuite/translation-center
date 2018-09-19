<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation;

use MageSuite\TranslationCenter\Service\Translation\Importer;
use MageSuite\TranslationCenter\Service\Translation\ImporterInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\File\Csv as CsvProcessor;

class ImporterTest extends TestCase
{
    /**
     * @var CsvProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $csvProcessorMock;

    /**
     * @var Importer
     */
    protected $importerInstance;

    protected function setUp()
    {
        $this->csvProcessorMock = $this->getMockBuilder(CsvProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importerInstance = new Importer($this->csvProcessorMock);
    }

    public function testImporterCanBeInstantiated()
    {
        $this->assertInstanceOf(Importer::class, $this->importerInstance);
    }

    public function testImporterImplementsImporterInterface()
    {
        $this->assertInstanceOf(ImporterInterface::class, $this->importerInstance);
    }

    public function testImportMethodCallsSaveDataOnCsvProcessor()
    {
        $dummyData = [];
        $dummyTargetPath = '/tmp/dummy.csv';

        $this->csvProcessorMock
            ->expects($this->once())
            ->method('saveData')
            ->with($this->equalTo($dummyTargetPath), $this->equalTo($dummyData));

        $this->importerInstance->import($dummyData, $dummyTargetPath);
    }
}
