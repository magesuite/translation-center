<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 - 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Controller\Adminhtml\Import;

use MageSuite\TranslationCenter\Service\ConfigInterface;
use MageSuite\TranslationCenter\Service\LanguagePackage\GeneratorInterface;
use MageSuite\TranslationCenter\Service\LanguagePackage\LocatorInterface;
use MageSuite\TranslationCenter\Service\Translation\AggregatorInterface;
use MageSuite\TranslationCenter\Service\Translation\ImporterInterface;
use Magento\Backend\App\Action;

/**
 * Translations import form submit controller
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Save extends Action
{
    /**
     * @var AggregatorInterface
     */
    protected $translationAggregator;

    /**
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * Import submit controller constructor
     *
     * @param Action\Context $context
     * @param AggregatorInterface $translationAggregator
     * @param ImporterInterface $importer
     * @param ConfigInterface $config
     * @param LocatorInterface $locator
     * @param GeneratorInterface $generator
     */
    public function __construct(
        Action\Context $context,
        AggregatorInterface $translationAggregator,
        ImporterInterface $importer,
        ConfigInterface $config,
        LocatorInterface $locator,
        GeneratorInterface $generator
    ) {
        $this->translationAggregator = $translationAggregator;
        $this->importer = $importer;
        $this->locator = $locator;
        $this->generator = $generator;
        parent::__construct($context);
        $this->config = $config;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $locale = $this->getRequest()->getParam('locale', null);

        try {
            $targetDir = $this->locator->locateLanguagePackage($locale, $this->config->getLanguagePackageVendor());

            if (null === $targetDir) {
                $targetDir = $this->generator->generateLanguagePackage(
                    $locale,
                    $this->config->getLanguagePackageVendor(),
                    $this->config->getLanguagePackageParentVendor()
                );
            }

            $this->importer->import(
                $this->translationAggregator->fetchSystemTranslations($locale),
                $targetDir . DIRECTORY_SEPARATOR . $locale . '.csv'
            );
            $this->messageManager->addSuccessMessage(
                sprintf('Translations for \'%s\' locale were imported successfully', $locale)
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                'Error occurred when importing translations! See logs for more details.'
            );
        }
        $resultRedirect = $this->resultRedirectFactory->create()->setPath('*/*');
        return $resultRedirect;
    }
}
