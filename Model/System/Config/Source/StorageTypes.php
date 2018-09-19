<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Translations storage types list
 */
class StorageTypes implements ArrayInterface
{
    /**
     * @var array
     */
    protected $storageTypes;

    /**
     * Translations storage types list constructor
     *
     * @param array $storageTypes
     */
    public function __construct(array $storageTypes = [])
    {
        $this->storageTypes = $storageTypes;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: [['value' => '<storage class>', 'label' => '<storage label>'], ...]
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->storageTypes as $storageClass => $storageLabel) {
            $options[] = [
                'value' => $storageClass,
                'label' => $storageLabel
            ];
        }
        return $options;
    }
}
