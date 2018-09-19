<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Block\Adminhtml\Import\Edit;

use MageSuite\TranslationCenter\Model\System\Import\Source\Locale as LocaleSource;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends Generic
{
    /**
     * @var LocaleSource
     */
    protected $localeSource;

    /**
     * Translations import form constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param LocaleSource $localeSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        LocaleSource $localeSource,
        array $data = []
    ) {
        $this->localeSource = $localeSource;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post'
            ]
        ]);

        $fieldsets = [
            'base' => $form->addFieldset('base_fieldset', [])
        ];

        $fieldsets['base']->addField(
            'locale',
            'select',
            [
                'name' => 'locale',
                'title' => __('Locale'),
                'label' => __('Locale'),
                'required' => true,
                'values' => $this->localeSource->toOptionArray()
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
