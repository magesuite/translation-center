<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Service\Translation\Storage\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\RequestFactory;
use MageSuite\TranslationCenter\Test\TestCase;

class RequestFactoryTest extends TestCase
{
    /**
     * @var RequestFactory
     */
    protected $factoryInstance;

    protected function setUp()
    {
        $this->factoryInstance = new RequestFactory();
    }

    /**
     * @param string $requestType
     * @return string
     */
    protected function getCreateResultClass($requestType)
    {
        return preg_replace(
            '/^(.+_)(Request)$/',
            '$1' . ucfirst($requestType) . '$2',
            \Google_Service_Sheets_Request::class
        );
    }

    public function testRequestFactoryCanBeInstantiated()
    {
        $this->assertInstanceOf(RequestFactory::class, $this->factoryInstance);
    }

    /**
     * @param string $requestType
     * @param array $params
     *
     * @dataProvider requestParamsProvider
     */
    public function testCreateMethodReturnsRequestInstance($requestType, array $params)
    {
        $requestInstance = $this->factoryInstance->create($requestType, $params);
        $this->assertInstanceOf(\Google_Service_Sheets_Request::class, $requestInstance);
    }

    /**
     * @param string $requestType
     * @param array $params
     *
     * @dataProvider requestParamsProvider
     */
    public function testCreateBatchUpdateSpreadsheetMethodReturnsBatchRequestInstance($requestType, array $params)
    {
        $requestInstance = $this->factoryInstance->createBatchUpdateSpreadsheet([$requestType => $params]);
        $this->assertInstanceOf(\Google_Service_Sheets_BatchUpdateSpreadsheetRequest::class, $requestInstance);
    }

    /**
     * @return array
     */
    public function requestParamsProvider()
    {
        return [
            ['addSheet', []],
            ['deleteSheet', []],
            ['updateCells', []]
        ];
    }
}
