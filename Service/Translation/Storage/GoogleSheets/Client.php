<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

/**
 * Google Sheets API connection manager
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Client
{
    /**
     * @var \Google_Client
     */
    private $googleClient;

    /**
     * @var CredentialsProviderInterface
     */
    private $googleSheetsCredentials;

    /**
     * Google Sheets connection manager constructor
     *
     * @param \Google_Client $googleClient
     * @param CredentialsProviderInterface $googleSheetsCredentials
     * @param ConfigInterface $googleSheetsConfig
     */
    public function __construct(
        \Google_Client $googleClient,
        CredentialsProviderInterface $googleSheetsCredentials,
        ConfigInterface $googleSheetsConfig
    ) {
        $this->googleClient = $googleClient;
        $this->googleSheetsCredentials = $googleSheetsCredentials;

        $this->googleClient->setApplicationName($googleSheetsConfig->getApplicationName());
        $this->googleClient->setScopes(implode(' ', $googleSheetsConfig->getScopes()));
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setAuthConfig($this->googleSheetsCredentials->getSecret());

        $accessToken = $this->googleSheetsCredentials->getAccessToken();

        if (!empty($accessToken)) {
            $this->googleClient->setAccessToken($accessToken);
            if ($this->googleClient->isAccessTokenExpired() && $this->googleClient->getRefreshToken()) {
                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
                $this->googleSheetsCredentials->setAccessToken($this->googleClient->getAccessToken() ?: []);
            }
        }
    }

    /**
     * @param string $redirectUri
     * @return string
     */
    public function getAuthUrl($redirectUri)
    {
        $this->googleClient->setRedirectUri($redirectUri);
        return $this->googleClient->createAuthUrl();
    }

    /**
     * @param string $authCode
     * @param string $redirectUri
     */
    public function authenticate($authCode, $redirectUri)
    {
        $this->googleClient->setRedirectUri($redirectUri);
        $this->googleClient->fetchAccessTokenWithAuthCode($authCode);
        $this->googleSheetsCredentials->setAccessToken($this->googleClient->getAccessToken() ?: []);
    }

    public function unlink()
    {
        $this->googleSheetsCredentials->setAccessToken([]);
    }

    public function revokeToken()
    {
        $this->googleClient->revokeToken();
    }

    /**
     * @return boolean
     */
    public function isAuthenticated()
    {
        $accessToken = $this->googleClient->getAccessToken();
        return !empty($accessToken);
    }

    /**
     * @return array|null
     */
    public function getUserProfile()
    {
        $accessToken = $this->googleSheetsCredentials->getAccessToken();
        if (!empty($accessToken) && array_key_exists('id_token', $accessToken)) {
            return $this->googleClient->verifyIdToken($accessToken['id_token']) ?: null;
        }
        return null;
    }
}
