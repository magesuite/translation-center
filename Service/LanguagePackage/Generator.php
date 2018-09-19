<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 - 2017 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\LanguagePackage;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Custom language package generator
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Generator implements GeneratorInterface
{
    const FILE_NAME_LANGUAGE_XML = 'language.xml';

    const FILE_NAME_REGISTRATION_PHP = 'registration.php';

    /**
     * @var string
     */
    protected $languagePackagesDir;

    public function __construct(DirectoryList $directoryList)
    {
        $this->languagePackagesDir = $directoryList->getPath(DirectoryList::APP) . DIRECTORY_SEPARATOR . 'i18n';
    }

    /**
     * @param string $targetPath
     * @param string $content
     * @throws \Exception
     */
    protected function writeFileContent($targetPath, $content)
    {
        $fileHandle = fopen($targetPath, 'w');

        if (false === $fileHandle) {
            throw new \Exception(sprintf('Error while opening \'%s\' file for writing!', $targetPath));
        }

        fwrite($fileHandle, $content);
        fclose($fileHandle);
    }

    /**
     * @param string $targetDir
     * @param string $locale
     * @param string $vendor
     * @param string|null $parentVendor
     */
    protected function generateLanguageXmlFile($targetDir, $locale, $vendor, $parentVendor = null)
    {
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . self::FILE_NAME_LANGUAGE_XML;

        $fileContent = <<<CONTENT
<?xml version="1.0"?>
<language xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:noNamespaceSchemaLocation="urn:magento:framework:App/Language/package.xsd">
    <code>%s</code>
    <vendor>%s</vendor>
    <package>%s</package>
    %s
</language>
CONTENT;

        $inheritance = $parentVendor
            ? sprintf('<use vendor="%s" package="%s"/>', $parentVendor, strtolower($locale)) : '';

        $this->writeFileContent($targetPath, sprintf(
            $fileContent,
            $locale,
            $vendor,
            strtolower($locale),
            $inheritance
        ));
    }

    /**
     * @param string $targetDir
     * @param string $locale
     * @param string $vendor
     */
    protected function generateRegistrationPhpFile($targetDir, $locale, $vendor)
    {
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . self::FILE_NAME_REGISTRATION_PHP;

        $fileContent = <<<CONTENT
<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::LANGUAGE,
    '%s_%s',
    __DIR__
);
CONTENT;

        $this->writeFileContent($targetPath, sprintf($fileContent, $vendor, strtolower($locale)));
    }

    /**
     * @inheritdoc
     */
    public function generateLanguagePackage($locale, $vendor, $parentVendor = null)
    {
        $languagePackageDir = implode(DIRECTORY_SEPARATOR, [$this->languagePackagesDir, $vendor, $locale]);

        if (file_exists($languagePackageDir)) {
            throw new \Exception(sprintf('\'%s\' language package directory already exists!', $languagePackageDir));
        }

        mkdir($languagePackageDir, 0770, true);
        $this->generateLanguageXmlFile($languagePackageDir, $locale, $vendor, $parentVendor);
        $this->generateRegistrationPhpFile($languagePackageDir, $locale, $vendor);

        return $languagePackageDir;
    }
}
