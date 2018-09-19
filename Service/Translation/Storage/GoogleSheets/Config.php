<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

use Magento\Framework\App\Config\ConfigResource\ConfigInterface as ResourceConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Google Sheets configuration
 */
class Config implements ConfigInterface
{
    /**
     * XML path to "Client ID" config option
     */
    const XML_PATH_CLIENT_ID = 'translation_center/google_sheets/client_id';

    /**
     * XML path to "Client secret" config option
     */
    const XML_PATH_CLIENT_SECRET = 'translation_center/google_sheets/client_secret';

    /**
     * XML path to "Spreadsheet" config option
     */
    const XML_PATH_SPREADSHEET_ID = 'translation_center/google_sheets/spreadsheet_id';

    /**
     * XML path to "Access token" config option
     */
    const XML_PATH_ACCESS_TOKEN = 'translation_center/google_sheets/access_token';

    /**
     * XML path to "Refresh token" config option
     */
    const XML_PATH_REFRESH_TOKEN = 'translation_center/google_sheets/refresh_token';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ResourceConfig
     */
    protected $resourceConfig;

    /**
     * Google Sheets configuration constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfig $resourceConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConfig = $resourceConfig;
    }

    /**
     * @inheritdoc
     */
    public function getClientId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getClientSecret()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET);
    }

    /**
     * @inheritdoc
     */
    public function getSpreadsheetId()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SPREADSHEET_ID);
    }

    /**
     * @inheritdoc
     */
    public function getApplicationName()
    {
        return 'Magento 2 Constructor / Translation Center';
    }

    /**
     * @inheritdoc
     */
    public function getScopes()
    {
        return ['profile', \Google_Service_Sheets::SPREADSHEETS, \Google_Service_Drive::DRIVE_METADATA_READONLY];
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        $accessTokenJson = $this->scopeConfig->getValue(self::XML_PATH_ACCESS_TOKEN);
        return $accessTokenJson ? json_decode($accessTokenJson, true) : [];
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken(array $accessToken)
    {
        $accessTokenJson = json_encode($accessToken, JSON_FORCE_OBJECT);
        $this->resourceConfig->saveConfig(self::XML_PATH_ACCESS_TOKEN, $accessTokenJson, 'default', 0);
    }

    /**
     * @inheritdoc
     */
    public function getRefreshToken()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_REFRESH_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setRefreshToken($refreshToken)
    {
        $this->resourceConfig->saveConfig(self::XML_PATH_REFRESH_TOKEN, $refreshToken, 'default', 0);
    }
}
