<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets;

use Magento\Framework\App\Cache\TypeListInterface;

/**
 * Google API credentials provider
 */
class CredentialsProvider implements CredentialsProviderInterface
{
    const REFRESH_TOKEN_ARRAY_KEY = 'refresh_token';

    /**
     * @var ConfigInterface
     */
    protected $googleConfig;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * Google API credentials provider constructor
     *
     * @param ConfigInterface $googleConfig
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        ConfigInterface $googleConfig,
        TypeListInterface $cacheTypeList
    ) {
        $this->googleConfig = $googleConfig;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * @param array $accessTokenArray
     * @param string $propertyName
     * @return string|null
     */
    protected function getAccessTokenProperty(array $accessTokenArray, $propertyName)
    {
        if (array_key_exists($propertyName, $accessTokenArray)) {
            return $accessTokenArray[$propertyName];
        }
        return null;
    }

    /**
     * @param array $accessTokenArray
     * @param string $propertyName
     * @param string $propertyValue
     */
    protected function setAccessTokenProperty(array &$accessTokenArray, $propertyName, $propertyValue)
    {
        if (!array_key_exists($propertyName, $accessTokenArray) || $accessTokenArray[$propertyName] == '') {
            $accessTokenArray[$propertyName] = $propertyValue;
        }
    }

    /**
     * @inheritdoc
     */
    public function getSecret()
    {
        $secret = ['web' => [
            'client_id' => $this->googleConfig->getClientId(),
            'client_secret' => $this->googleConfig->getClientSecret()
        ]];
        return $secret;
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        $accessToken = $this->googleConfig->getAccessToken();
        if (!empty($accessToken)) {
            $refreshToken = $this->googleConfig->getRefreshToken();
            if ($refreshToken) {
                $this->setAccessTokenProperty(
                    $accessToken,
                    self::REFRESH_TOKEN_ARRAY_KEY,
                    $refreshToken
                );
            }
            return $accessToken;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken(array $accessToken)
    {
        if (!empty($accessToken)) {
            $refreshToken = $this->getAccessTokenProperty($accessToken, self::REFRESH_TOKEN_ARRAY_KEY);
            if ($refreshToken) {
                $this->googleConfig->setRefreshToken($refreshToken);
            }
        }
        $this->googleConfig->setAccessToken($accessToken);
        $this->cacheTypeList->cleanType('config');
    }
}
