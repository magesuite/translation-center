<?php
/**
 * Translation Center for Magento 2
 *
 * @author    Marek Zabrowarny <marek.zabrowarny@creativestyle.pl>
 * @copyright 2016 creativestyle
 */


namespace MageSuite\TranslationCenter\Block\Adminhtml\System\Config\GoogleSheets;

use MageSuite\TranslationCenter\Service\Translation\Storage\GoogleSheets\Client;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @method string getHtmlId()
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Link extends Field
{
    /**
     * @var boolean
     */
    protected $isAuthenticated = null;

    /**
     * @var array|null
     */
    protected $userProfile = null;

    /**
     * @var string
     */
    protected $_template = 'system/config/google_sheets/link.phtml';

    /**
     * @var Client
     */
    private $googleClient;

    /**
     * Google Sheets link block constructor
     *
     * @param Context $context
     * @param Client $googleClient
     * @param array $data
     */
    public function __construct(Context $context, Client $googleClient, array $data = [])
    {
        parent::__construct($context, $data);
        $this->googleClient = $googleClient;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->addData(['html_id' => $element->getHtmlId()
        ]);
        $element->setValue($this->isAuthenticated() ? 1 : 0);
        return parent::_getElementHtml($element) . $this->toHtml();
    }

    /**
     * Check if user is authenticated
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        if (null === $this->isAuthenticated) {
            $this->isAuthenticated = $this->googleClient->isAuthenticated();
        }
        return $this->isAuthenticated;
    }

    /**
     * Get authenticated user's profile or return null if not available
     *
     * @return array|null
     */
    public function getUserProfile()
    {
        if (null === $this->userProfile) {
            $this->userProfile = $this->googleClient->getUserProfile();
        }
        return $this->userProfile;
    }

    /**
     * Get authenticated user's name or return null if not available
     *
     * @return string|null
     */
    public function getUserName()
    {
        $userProfile = $this->getUserProfile();
        if (!empty($userProfile) && array_key_exists('name', $userProfile)) {
            return $userProfile['name'];
        }
        return null;
    }

    /**
     * Get authenticated user's e-mail or return null if not available
     *
     * @return string|null
     */
    public function getUserEmail()
    {
        $userProfile = $this->getUserProfile();
        if (!empty($userProfile) && array_key_exists('email', $userProfile)) {
            return $userProfile['email'];
        }
        return null;
    }

    /**
     * Get authenticated user's picture URL or return null if not available
     *
     * @return string|null
     */
    public function getUserPicture()
    {
        $userProfile = $this->getUserProfile();
        if (!empty($userProfile) && array_key_exists('picture', $userProfile)) {
            return $userProfile['picture'];
        }
        return null;
    }
}
