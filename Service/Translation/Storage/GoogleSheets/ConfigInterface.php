<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

/**
 * Google Sheets configuration interface
 */
interface ConfigInterface
{
    /**
     * Return Google API client ID
     *
     * @return string
     */
    public function getClientId();

    /**
     * Return Google API client secret
     *
     * @return string
     */
    public function getClientSecret();

    /**
     * Return Google spreadsheet ID
     *
     * @return string
     */
    public function getSpreadsheetId();

    /**
     * Return Google API client application name
     *
     * @return string
     */
    public function getApplicationName();

    /**
     * Return Google API authorization scopes
     *
     * @return array
     */
    public function getScopes();

    /**
     * Return current Google API access token
     *
     * @return array
     */
    public function getAccessToken();

    /**
     * Sets new Google API access token
     *
     * @param array $accessToken
     * @return void
     */
    public function setAccessToken(array $accessToken);

    /**
     * Return current Google API refresh token
     *
     * @return string
     */
    public function getRefreshToken();

    /**
     * Sets new Google API refresh token
     *
     * @param string $refreshToken
     * @return void
     */
    public function setRefreshToken($refreshToken);
}
