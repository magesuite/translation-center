<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private $dataCache = [];

    private function getDataValues($type)
    {
        if (!array_key_exists($type, $this->dataCache)) {
            $this->dataCache[$type] = $this->fetchDataValuesFromXmlFile($type);
        }
        return $this->dataCache[$type];
    }

    private function fetchDataValuesFromXmlFile($type)
    {
        $xmlFilePath = sprintf('%s/_data/%s.xml', __DIR__, $type);
        $items = simplexml_load_file($xmlFilePath);
        if (isset($items->item)) {
            $data = [];
            /** @var \SimpleXMLElement $item */
            foreach ($items->item as $item) {
                $data[] = $this->parseXmlNode($item);
            }
            return $data;
        }
        return null;
    }

    /**
     * @param \SimpleXMLElement $node
     * @return mixed
     */
    private function parseXmlNode(\SimpleXMLElement $node)
    {
        switch ($node->attributes()->type) {
            case 'array':
                $result = [];
                foreach ($node as $key => $value) {
                    $result[$key] = $this->parseXmlNode($value);
                }
                return $result;
            case 'int':
                return (int)$node;
            case 'float':
                return (float)$node;
            case 'bool':
                return !in_array(strtolower((string)$node), ['false', '0', 'no', '']);
            case 'null':
                return null;
            default:
                return (string)$node;
        }
    }

    /**
     * @param array $dataTypes
     * @return array
     */
    protected function getCartesianProductOfDataValues(array $dataTypes)
    {
        $result = [];
        $dataValues = array_map([$this, 'getDataValues'], $dataTypes);
        $dataTypeCount = array_map('count', $dataValues);
        for ($i = 0; $i < array_product($dataTypeCount); $i++) {
            $iterationElement = [];
            $tempIndex = $i;
            foreach (array_keys($dataTypes) as $dataType) {
                $iterationElement[$dataType] = $dataValues[$dataType][$tempIndex % $dataTypeCount[$dataType]];
                $tempIndex = ($tempIndex - $tempIndex % $dataTypeCount[$dataType]) / $dataTypeCount[$dataType];
            }
            $result[] = $iterationElement;
        }
        return $result;
    }

    /**
     * @return \Closure
     */
    protected function getArrayWrapCallback()
    {
        return function ($value) {
            return [$value];
        };
    }

    /**
     * @param array ... Variable number of arrays used to generate a cartesian product
     * @return array
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function getDataProvidersCartesianProduct()
    {
        $dataProviders = func_get_args();
        $firstDataProvider = array_shift($dataProviders);

        if (empty($dataProviders)) {
            return $firstDataProvider;
        }

        $cartesianProduct = [];
        foreach ($firstDataProvider as $firstDataProviderSet) {
            foreach (call_user_func_array([$this, __FUNCTION__], $dataProviders) as $secondDataProviderSet) {
                $cartesianProduct[] = array_merge($firstDataProviderSet, $secondDataProviderSet);
            }
        }
        return $cartesianProduct;
    }

    /**
     * @param callable $callback
     * @param array ... Variable number of arrays passed as arguments to callback function
     * @return array
     */
    protected function mapDataProviders(callable $callback)
    {
        return array_map(
            $this->getArrayWrapCallback(),
            call_user_func_array('array_map', func_get_args())
        );
    }

    /**
     * @param array ... Variable number of arrays to merge
     * @return array
     */
    protected function mergeDataProviders()
    {
        $args = func_get_args();
        array_unshift($args, 'array_merge');
        return call_user_func_array('array_map', $args);
    }

    /**
     * @param array $dataProvider
     * @return array
     */
    protected function implodeDataProvider(array $dataProvider)
    {
        return [[call_user_func_array('array_merge', $dataProvider)]];
    }

    /**
     * @param string $rangeAddress
     * @return array
     */
    protected function expandRange($rangeAddress)
    {
        $boundaryColumns = $boundaryRows = [];
        $boundaryCells = explode(':', $rangeAddress);
        if (preg_match('/^([A-Z])[0-9]*$/i', $boundaryCells[0], $matches)) {
            $boundaryColumns[] = ord($matches[1]);
        }
        if (preg_match('/^([A-Z])[0-9]*$/i', $boundaryCells[1], $matches)) {
            $boundaryColumns[] = ord($matches[1]);
        }
        if (preg_match('/^[A-Z]*([0-9]+)$/i', $boundaryCells[0], $matches)) {
            $boundaryRows[] = $matches[1];
        }
        if (preg_match('/^[A-Z]*([0-9]+)$/i', $boundaryCells[1], $matches)) {
            $boundaryRows[] = $matches[1];
        }
        if (count($boundaryColumns) < 2) {
            $boundaryColumns[] = $boundaryColumns[0] + 5;
        }
        if (count($boundaryRows) < 2) {
            $boundaryRows[] = $boundaryRows[0] + 5;
        }
        $expandedRange = [];
        for ($i = min($boundaryRows); $i <= max($boundaryRows); $i++) {
            $row = [];
            for ($j = min($boundaryColumns); $j <= max($boundaryColumns); $j++) {
                $row[] = sprintf('%s%d', chr($j), $i);
            }
            $expandedRange[] = $row;
        }
        return $expandedRange;
    }

    /**
     * @param string $type
     * @return null|string
     */
    protected function getJsonData($type)
    {
        $jsonFilePath = sprintf('%s/_data/%s.json', __DIR__, $type);
        if (file_exists($jsonFilePath)) {
            return file_get_contents($jsonFilePath);
        }
        return null;
    }

    /**
     * @return array
     */
    public function translationPairProvider()
    {
        return [
            ['Lorem ipsum dolor sit amet', 'Consectetur adipiscing elit'],
            ['Fusce ut efficitur tellus', 'Duis eu orci porta'],
            ['Donec ut ex sit amet sapien: %s', '%s: donec eget leo sit amet sem']
        ];
    }

    /**
     * @return array
     */
    public function localeProvider()
    {
        return [
            ['en_US'],
            ['de_DE'],
            ['pl_PL']
        ];
    }

    /**
     * @return array
     */
    public function translationPrependMetadataProvider()
    {
        return [
            [['type' => 'module', 'name' => 'MageSuite_TranslationCenter']]
        ];
    }

    /**
     * @return array
     */
    public function translationUrlMetadataProvider()
    {
        return [
            [['url' => 'http://shop.tld/en_US/']],
            [['url' => 'http://shop.tld/de_DE/']],
            [['url' => 'http://shop.tld/pl_PL/']]
        ];
    }

    public function translationMetadataProvider()
    {
        return $this->getDataProvidersCartesianProduct(
            $this->translationPrependMetadataProvider(),
            $this->translationUrlMetadataProvider()
        );
    }

    /**
     * @return array
     */
    public function translationRowProvider()
    {
        return $this->getDataProvidersCartesianProduct(
            $this->translationPairProvider(),
            $this->mergeDataProviders($this->localeProvider(), $this->translationUrlMetadataProvider())
        );
    }

    /**
     * @return array
     */
    public function remoteTranslationDataProvider()
    {
        $cartesian = $this->getDataProvidersCartesianProduct(
            [[$this->translationPairProvider()]],
            $this->localeProvider()
        );
        return $cartesian;
    }
}
