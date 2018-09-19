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
 * Google API OAuth token exchange callback controller
 */
class AuthCallback extends Action
{
    /**
     * @var Client
     */
    private $googleClient;

    /**
     * @inheritdoc
     */
    protected $_publicActions = ['authCallback'];

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
        $authCode = $this->getRequest()->getParam('code', null);
        if (null !== $authCode) {
            $redirectUri = $this->getUrl('*/*/*', ['_nosecret' => true]);
            $this->googleClient->authenticate($authCode, $redirectUri);
        } else {
            $error = $this->getRequest()->getParam('error', '');
            $this->messageManager->addErrorMessage(
                sprintf('Google API authorization error: %s', $error)
            );
        }
        $redirectResult = $this->resultRedirectFactory->create()
            ->setPath('adminhtml/system_config/edit', ['section' => 'translation_center']);
        return $redirectResult;
    }
}
