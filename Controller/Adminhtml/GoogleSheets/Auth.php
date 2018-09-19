<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Controller\Adminhtml\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client;
use Magento\Backend\App\Action;

/**
 * Google API OAuth redirect controller
 */
class Auth extends Action
{
    /**
     * @var Client
     */
    private $googleClient;

    public function __construct(
        Action\Context $context,
        Client $googleClient
    ) {
        parent::__construct($context);
        $this->googleClient = $googleClient;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $redirectUri = $this->_backendUrl->getUrl('translation_center/googleSheets/authCallback', ['_nosecret' => true]);
        $authUrl = $this->googleClient->getAuthUrl($redirectUri);
        $resultRedirect = $this->resultRedirectFactory->create()->setUrl($authUrl);
        return $resultRedirect;
    }
}
