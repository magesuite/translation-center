<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Integration\Controller\Adminhtml\Import;

use Magento\TestFramework\Request;
use Magento\TestFramework\TestCase\AbstractController as ControllerTestCase;

class IndexTest extends ControllerTestCase
{
    public function testControllerHandlesGetRequests()
    {
        $this->getRequest()->setMethod(Request::METHOD_GET);
        $this->dispatch('backend/translation_center/import/index');
        $this->assertSame(302, $this->getResponse()->getHttpResponseCode());
    }
}
