<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Plugin\Translation\Model\Json;

use Magento\Framework\Event\ManagerInterface;
use Magento\Translation\Model\Json\PreProcessor;

class PreProcessorPlugin
{
    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ManagerInterface $eventManager
     */
    public function __construct(ManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @param PreProcessor $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterProcess(PreProcessor $subject, $result)
    {
        $this->eventManager->dispatch('translation_json_pre_processor_after');
        return $result;
    }
}
