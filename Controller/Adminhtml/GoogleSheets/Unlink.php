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
 * Google API OAuth token disconnect controller
 */
class Unlink extends Action
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
        $revokeToken = $this->getRequest()->getParam('revoke', false);
        $this->googleClient->unlink();
        if ($revokeToken) {
            $this->googleClient->revokeToken();
        }
        return $this->resultRedirectFactory->create()
            ->setPath('adminhtml/system_config/edit', ['section' => 'translation_center']);
    }
}
