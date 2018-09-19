<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

/**
 * Google API credentials provider interface
 */
interface CredentialsProviderInterface
{
    /**
     * Return array comprised of client ID and client secret
     *
     * @return array|null
     */
    public function getSecret();

    /**
     * Return access token array
     *
     * @return array|null
     */
    public function getAccessToken();

    /**
     * Set and save new access token
     *
     * @param array $accessToken
     * @return void
     */
    public function setAccessToken(array $accessToken);
}
