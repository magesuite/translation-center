<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Console\Command;

use MageSuite\TranslationCenter\Service\ConfigInterface;
use MageSuite\TranslationCenter\Service\LanguagePackage\GeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateLanguagePackageCommand extends Command
{
    const INPUT_KEY_LOCALE = 'locale';

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var ConfigInterface
     */
    protected $config;

    public function __construct(
        GeneratorInterface $generator,
        ConfigInterface $config
    ) {
        $this->generator = $generator;
        $this->config = $config;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('i18n:create-language-package')
            ->setDescription('Create language package for given locale');

        $this->setDefinition([
            new InputArgument(
                self::INPUT_KEY_LOCALE,
                InputArgument::REQUIRED,
                'Locale code of the language package'
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getArgument(self::INPUT_KEY_LOCALE);
        try {
            $this->generator->generateLanguagePackage(
                $locale,
                $this->config->getLanguagePackageVendor(),
                $this->config->getLanguagePackageParentVendor()
            );
            $output->writeln(
                sprintf('<info>Language pack for \'%s\' locale has been generated</info>', $locale)
            );
        } catch (\Exception $e) {
            $output->writeln(
                '<error>Error occurred when generating language pack!</error>' . PHP_EOL . $e->getMessage()
            );
        }
    }
}
