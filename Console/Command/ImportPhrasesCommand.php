<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Console\Command;

use MageSuite\TranslationCenter\Service\Translation\AggregatorInterface;
use MageSuite\TranslationCenter\Service\Translation\ImporterInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ImportPhrasesCommand extends Command
{
    const INPUT_KEY_LOCALE = 'locale';
    const INPUT_KEY_LANGUAGE_PACK = 'language_pack';

    /**
     * @var ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var AggregatorInterface
     */
    protected $translationAggregator;

    /**
     * @var ImporterInterface
     */
    protected $importer;

    public function __construct(
        ComponentRegistrar $componentRegistrar,
        AggregatorInterface $translationAggregator,
        ImporterInterface $importer
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->translationAggregator = $translationAggregator;
        $this->importer = $importer;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('i18n:import-phrases')
            ->setDescription('Import phrases from the configured translation storage');
        $this->setDefinition([
            new InputArgument(
                self::INPUT_KEY_LOCALE,
                InputArgument::REQUIRED,
                'Locale code of the importing phrases'
            ),
            new InputArgument(
                self::INPUT_KEY_LANGUAGE_PACK,
                InputArgument::REQUIRED,
                'Name of the language pack the phrases shall be imported to'
            )
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getArgument(self::INPUT_KEY_LOCALE);
        $languagePack = $input->getArgument(self::INPUT_KEY_LANGUAGE_PACK);
        $languagePackDir = $this->componentRegistrar->getPath(ComponentRegistrar::LANGUAGE, $languagePack);
        $targetPath = $languagePackDir . DIRECTORY_SEPARATOR . $locale . '.csv';
        try {
            $this->importer->import(
                $this->translationAggregator->fetchSystemTranslations($locale),
                $targetPath
            );
            $output->writeln(
                sprintf('<info>Translations for \'%s\' locale were imported successfully</info>', $locale)
            );
        } catch (\Exception $e) {
            $output->writeln(
                '<error>Error occurred when importing translations!</error>' . PHP_EOL . $e->getMessage()
            );
        }
    }
}
