<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Model\System\Config\Source\GoogleSheets;

use MageSuite\TranslationCenter\Model\System\Config\Source\GoogleSheets\Spreadsheets;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\TestFramework\ObjectManager;

/**
 * @SuppressWarnings(PHPMD.LongVariables)
 */
class SpreadsheetsTest extends TestCase
{
    /**
     * @var \Google_Service_Drive|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $googleServiceDriveMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->googleServiceDriveMock = $this->getMockBuilder(\Google_Service_Drive::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->addSharedInstance($this->googleServiceDriveMock, 'googleServiceDrive');
    }

    protected function prepareGoogleDriveServiceMock()
    {
        $response = $this->getMockBuilder(\Google_Service_Drive_FileList::class)
            ->getMock();
        $response->files = json_decode($this->getJsonData('GoogleApi/responses/files/listFiles'));

        $googleServiceDriveResourceFilesMock = $this->getMockBuilder(\Google_Service_Drive_Resource_Files::class)
            ->disableOriginalConstructor()
            ->getMock();

        $googleServiceDriveResourceFilesMock
            ->method('listFiles')
            ->willReturn($response);

        $this->googleServiceDriveMock->files = $googleServiceDriveResourceFilesMock;
    }

    protected function prepareGoogleDriveServiceMockToThrowException()
    {
        $googleServiceDriveResourceFilesMock = $this->getMockBuilder(\Google_Service_Drive_Resource_Files::class)
            ->disableOriginalConstructor()
            ->getMock();

        $googleServiceDriveResourceFilesMock
            ->method('listFiles')
            ->will($this->throwException(new \Google_Exception('Google exception simulation')));

        $this->googleServiceDriveMock->files = $googleServiceDriveResourceFilesMock;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function extractOptionLabels(array $options)
    {
        return array_map(
            function ($option) {
                return (string)$option['label'];
            },
            $options
        );
    }

    /**
     * @param array $options
     * @return array
     */
    protected function extractOptionValues(array $options)
    {
        return array_map(
            function ($option) {
                return $option['value'];
            },
            $options
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToOptionArrayMethodReturnsArrayWithTranslationSpreadsheet()
    {
        $this->prepareGoogleDriveServiceMock();
        $spreadsheetsSource = $this->objectManager->create(Spreadsheets::class);
        $optionLabels = $this->extractOptionLabels($spreadsheetsSource->toOptionArray());
        $this->assertContains(
            'Translation spreadsheet',
            $optionLabels
        );
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToOptionArrayMethodReturnsArrayWithEmptyValueOnGoogleDriveServiceException()
    {
        $this->prepareGoogleDriveServiceMockToThrowException();
        $spreadsheetsSource = $this->objectManager->create(Spreadsheets::class);
        $optionValues = $this->extractOptionValues($spreadsheetsSource->toOptionArray());
        $this->assertContains('', $optionValues);
    }
}
