<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_ProductMatrix
 * @copyright Copyright (C) 2018 Magezon (https://www.magezon.com)
 */

namespace Magezon\ProductMatrix\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context         
     * @param \Magento\Framework\App\RequestInterface    $request         
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager    
     * @param \Magento\Customer\Model\Session            $customerSession 
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_request        = $request;
        $this->_storeManager   = $storeManager;
        $this->customerSession = $customerSession;
    }
   
    /**
     * @param  string $key
     * @param  null|int $store
     * @return null|string
     */
    public function getConfig($key, $store = null)
    {
		$store     = $this->_storeManager->getStore($store);
		$websiteId = $store->getWebsiteId();
        $result = $this->scopeConfig->getValue(
            'productmatrix/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        $module     = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action     = $this->_request->getActionName();

        if ($module == 'checkout' && $controller == 'cart' && $action == 'configure') {
            return false;
        }

        $customerGroups = $this->getConfig('general/custom_groups');
        if ($customerGroups) {
            $customerGroups = explode(',', $customerGroups);
            if (!in_array('all', $customerGroups) && !in_array($this->customerSession->getCustomerGroupId(), $customerGroups)) {
                return false;
            }
        }

    	return $this->getConfig('general/enabled');
    }
}