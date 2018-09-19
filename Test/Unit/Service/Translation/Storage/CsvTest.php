<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Service\Translation\Storage;

use MageSuite\TranslationCenter\Service\Translation\Storage\Csv as CsvStorage;
use MageSuite\TranslationCenter\Service\Translation\Storage\SynchroniseStrategyInterface;
use MageSuite\TranslationCenter\Service\Translation\StorageInterface;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv as CsvProcessor;
use org\bovigo\vfs\vfsStream;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CsvTest extends TestCase
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    protected $fsRoot;

    /**
     * @var CsvProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $csvProcessorMock;

    /**
     * @var SynchroniseStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $synchroniseStrategyMock;

    /**
     * @var CsvStorage
     */
    protected $storageInstance;

    /**
     * @var string
     */
    protected $csvDirectory = 'translation-center';

    protected function setUp()
    {
        $this->fsRoot = vfsStream::setup('var');

        $this->csvProcessorMock = $this->getMockBuilder(CsvProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->synchroniseStrategyMock = $this->getMockBuilder(SynchroniseStrategyInterface::class)
            ->getMock();

        /** @var DirectoryList|\PHPUnit_Framework_MockObject_MockObject $directoryListMock */
        $directoryListMock = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $directoryListMock->method('getPath')
            ->with($this->equalTo('var'))
            ->willReturn(vfsStream::url('var'));

        $this->storageInstance = new CsvStorage(
            $this->csvProcessorMock,
            $this->synchroniseStrategyMock,
            $directoryListMock
        );
    }

    /**
     * @param array $translationData
     * @param string $csvPath
     */
    protected function prepareCsvProcessorMockForFetchMethodCall(array $translationData, $csvPath)
    {
        $csvPath = $this->fsRoot->getChild($csvPath)->url();
        $this->csvProcessorMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->equalTo($csvPath))
            ->willReturn($translationData);
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     */
    protected function prepareMocksForPersistMethodCall(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $translationData = [array_merge([$textToTranslate, $translatedText], $metadata)];
        $csvPath = $localeCode . '.csv';
        vfsStream::newFile($csvPath)->at($this->fsRoot->getChild($this->csvDirectory));
        $csvPath = $this->fsRoot->getChild($this->csvDirectory . DIRECTORY_SEPARATOR . $csvPath)->url();

        $this->synchroniseStrategyMock
            ->expects($this->once())
            ->method('synchronise')
            ->with($this->equalTo($translationData), $this->equalTo($translationData))
            ->willReturn($translationData);

        $this->csvProcessorMock
            ->expects($this->once())
            ->method('getData')
            ->with($this->equalTo($csvPath))
            ->willReturn($translationData);

        $this->csvProcessorMock
            ->expects($this->once())
            ->method('saveData')
            ->with($this->equalTo($csvPath), $this->equalTo($translationData));
    }

    public function testCsvStorageCanBeInstantiated()
    {
        $this->assertInstanceOf(CsvStorage::class, $this->storageInstance);
    }

    public function testCsvStorageImplementsStorageInterface()
    {
        $this->assertInstanceOf(StorageInterface::class, $this->storageInstance);
    }

    public function testCsvStorageCreatesStorageDirectoryIfItDoesNotExist()
    {
        $this->assertTrue($this->fsRoot->hasChild($this->csvDirectory));
        $this->assertEquals(
            CsvStorage::TARGET_DIRECTORY_MODE,
            $this->fsRoot->getChild($this->csvDirectory)->getPermissions()
        );
    }

    /**
     * @param string $localeCode
     *
     * @dataProvider localeProvider
     */
    public function testFetchMethodReturnsEmptyArrayWhenCsvFileDoesNotExist($localeCode)
    {
        $this->assertSame([], $this->storageInstance->fetch($localeCode));
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     *
     * @dataProvider translationRowProvider
     */
    public function testFetchMethodReturnsTranslationDataArray(
        $textToTranslate,
        $translatedText,
        $localeCode
    ) {
        $translationData = [[$textToTranslate, $translatedText]];
        $csvPath = $localeCode . '.csv';

        vfsStream::newFile($csvPath)->at($this->fsRoot->getChild($this->csvDirectory));

        $this->prepareCsvProcessorMockForFetchMethodCall(
            $translationData,
            $this->csvDirectory . DIRECTORY_SEPARATOR . $csvPath
        );
        $this->assertSame($translationData, $this->storageInstance->fetch($localeCode));
    }

    /**
     * @param string $textToTranslate
     * @param string $translatedText
     * @param string $localeCode
     * @param array $metadata
     *
     * @dataProvider translationRowProvider
     */
    public function testPersistMethodSavesStoredDataToCsvFile(
        $textToTranslate,
        $translatedText,
        $localeCode,
        array $metadata
    ) {
        $this->storageInstance->add($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->prepareMocksForPersistMethodCall($textToTranslate, $translatedText, $localeCode, $metadata);
        $this->storageInstance->persist();
    }

}
