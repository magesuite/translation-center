<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SpreadsheetsList implements SpreadsheetsListInterface
{
    /**
     * @var \Google_Client
     */
    protected $googleClient;

    /**
     * @var \Google_Service_Drive
     */
    protected $googleService;

    /**
     * @var ConfigInterface
     */
    protected $googleSheetsConfig;

    /**
     * @var CredentialsProviderInterface
     */
    protected $googleSheetsCredentials;

    /**
     * @var array|null
     */
    protected $spreadsheetsCache = null;

    /**
     * @param \Google_Service_Drive $googleDriveService
     * @param Client $googleClientAdapter
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Google_Service_Drive $googleDriveService,
        Client $googleClientAdapter
    ) {
        $this->googleService = $googleDriveService;
    }

    /**
     * @return array
     */
    public function getSpreadsheets()
    {
        if (null === $this->spreadsheetsCache) {
            $response = $this->googleService->files->listFiles([
                'q' => "mimeType = 'application/vnd.google-apps.spreadsheet'"
            ]);
            if (isset($response->files)) {
                $this->spreadsheetsCache = $response->files;
            }
        }
        return $this->spreadsheetsCache;
    }
}
