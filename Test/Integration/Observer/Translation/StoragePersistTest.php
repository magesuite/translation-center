<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 - 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Observer\Translation;

use MageSuite\TranslationCenter\Observer\Translation\StoragePersist;
use MageSuite\TranslationCenter\Test\TestCase;
use Magento\Framework\Event\ConfigInterface as EventConfig;
use Magento\TestFramework\ObjectManager;

class StoragePersistTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var EventConfig
     */
    protected $eventConfig;

    /**
     * @var string[]
     */
    protected $globalEvents = [
        'translation_json_pre_processor_after',
        'controller_front_send_response_before'
    ];

    /**
     * @var string
     */
    protected $observerName = 'translation_center_observer_storage_persist';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
        $this->eventConfig = $this->objectManager->create(EventConfig::class);
    }

    /**
     * @param string $event
     * @return array
     */
    protected function getObservers($event)
    {
        return $this->eventConfig->getObservers($event);
    }

    public function testTranslationStoragePersistIsConfiguredToObserveAppropriateEventInGlobalScope()
    {
        foreach ($this->globalEvents as $event) {
            $observers = $this->getObservers($event);
            $this->assertArrayHasKey(
                $this->observerName,
                $observers,
                sprintf('Object is not configured to observe %s event in global scope!', $event)
            );
            $this->assertSame(
                StoragePersist::class,
                $observers[$this->observerName]['instance'],
                'Invalid class is assigned to the observer!'
            );
        }
    }
}
