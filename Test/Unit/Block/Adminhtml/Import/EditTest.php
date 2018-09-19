<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Test\Unit\Block\Adminhtml\Import;

use MageSuite\TranslationCenter\Block\Adminhtml\Import\Edit as EditBlock;
use MageSuite\TranslationCenter\Test\TestCase;

class EditTest extends TestCase
{
    /**
     * @var EditBlock
     */
    protected $blockInstance;

    protected function setUp()
    {
        /** @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject $urlBuilderStub */
        $urlBuilderStub = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Backend\Block\Widget\Button\ButtonList|\PHPUnit_Framework_MockObject_MockObject $buttonListStub */
        $buttonListStub = $this->getMockBuilder(\Magento\Backend\Block\Widget\Button\ButtonList::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject $requestStub */
        $requestStub = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Backend\Block\Widget\Context|\PHPUnit_Framework_MockObject_MockObject $contextStub */
        $contextStub = $this->getMockBuilder(\Magento\Backend\Block\Widget\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextStub->method('getUrlBuilder')
            ->willReturn($urlBuilderStub);

        $contextStub->method('getButtonList')
            ->willReturn($buttonListStub);

        $contextStub->method('getRequest')
            ->willReturn($requestStub);

        $this->blockInstance = new EditBlock($contextStub, []);
    }

    public function testItCanBeInstantiated()
    {
        $this->assertInstanceOf(EditBlock::class, $this->blockInstance);
    }

    public function testGetHeaderTextMethodReturnsPhraseInstance()
    {
        $this->assertInstanceOf(\Magento\Framework\Phrase::class, $this->blockInstance->getHeaderText());
    }
}
