<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SynchroniseStrategy implements SynchroniseStrategyInterface
{
    /**
     * @var string
     */
    protected $metadataGlue = "\n";

    /**
     * @param array $rows
     * @return array
     */
    protected function sanitizeRows($rows)
    {
        return array_filter($rows, function ($row) {
            return isset($row[0]) && isset($row[1]);
        });
    }

    /**
     * @param array $localTranslationPair
     * @param array $remoteTranslationPair
     * @return array
     */
    protected function synchroniseTranslationPairs(array $localTranslationPair, array $remoteTranslationPair)
    {
        if (!empty(array_diff($remoteTranslationPair, $localTranslationPair))) {
            return $remoteTranslationPair;
        }
        return $localTranslationPair;
    }

    /**
     * @param array $localMetadata
     * @param array $remoteMetadata
     * @return array
     */
    protected function synchroniseMetadata(array $localMetadata, array $remoteMetadata)
    {
        $synchronisedMetadata = [];
        $localMetadata = array_values($localMetadata);
        foreach ($localMetadata as $key => $localData) {
            $synchronisedMetadata[$key] = explode($this->metadataGlue, $localData);
            if (array_key_exists($key, $remoteMetadata)) {
                $remoteData = explode($this->metadataGlue, $remoteMetadata[$key]);
                $synchronisedMetadata[$key] = array_merge(
                    array_diff($remoteData, $synchronisedMetadata[$key]),
                    $synchronisedMetadata[$key]
                );
                unset($remoteMetadata[$key]);
            }
            $synchronisedMetadata[$key] = implode($this->metadataGlue, $synchronisedMetadata[$key]);
        }
        foreach ($remoteMetadata as $key => $remoteData) {
            $synchronisedMetadata[$key] = $remoteData;
        }
        return array_values($synchronisedMetadata);
    }

    /**
     * @param array $localRow
     * @param array $remoteRow
     * @return array
     */
    protected function synchroniseRows(array $localRow, array $remoteRow)
    {
        return array_merge(
            $this->synchroniseTranslationPairs(array_slice($localRow, 0, 2), array_slice($remoteRow, 0, 2)),
            $this->synchroniseMetadata(array_slice($localRow, 2), array_slice($remoteRow, 2))
        );
    }

    /**
     * @param array $localData
     * @param array $remoteData
     * @return array
     */
    public function synchronise(array $localData, array $remoteData)
    {
        $synchronisedData = [];
        $localData = $this->sanitizeRows($localData);
        $remoteData = $this->sanitizeRows($remoteData);
        $localData = array_combine(array_column($localData, 0), $localData);
        $remoteData = array_combine(array_column($remoteData, 0), $remoteData);
        foreach ($localData as $key => $localRow) {
            if (array_key_exists($key, $remoteData)) {
                $synchronisedData[$key] = $this->synchroniseRows($localRow, $remoteData[$key]);
                unset($remoteData[$key]);
            } else {
                $synchronisedData[$key] = $this->synchroniseRows($localRow, []);
            }
        }
        $synchronisedData = array_merge($synchronisedData, $remoteData);
        uksort($synchronisedData, 'strnatcasecmp');
        return array_values($synchronisedData);
    }
}
