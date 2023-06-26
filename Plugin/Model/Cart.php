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

namespace Magezon\ProductMatrix\Plugin\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;

class Cart
{
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var ProductRepositoryInterface
	 */
	protected $productRepository;

    /**
     * @var \Magezon\ProductMatrix\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager      
     * @param ProductRepositoryInterface                 $productRepository 
     * @param \Magezon\ProductMatrix\Helper\Data         $dataHelper        
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \Magezon\ProductMatrix\Helper\Data $dataHelper
    ) {
        $this->_storeManager     = $storeManager;
        $this->productRepository = $productRepository;
        $this->dataHelper        = $dataHelper;
    }

    public function aroundAddProduct(
    	\Magento\Checkout\Model\Cart $subject,
    	\Closure $proceed,
        $productInfo,
        $requestInfo = null
    ) {
    	if (isset($requestInfo['pmt_options']) && $requestInfo['pmt_options'] && $this->dataHelper->isEnabled()) {
            $pmtOptions = $requestInfo['pmt_options'];
    		$product = $this->_getProduct($productInfo);
    		if (isset($requestInfo['super_attribute'])) {
	    		$superAttribute = $requestInfo['super_attribute'];
	    		foreach ($pmtOptions as $k => $options) {
	    			foreach ($options as $optionId => $qty) {
	    				if ($qty) {
	    					$superRequest = $requestInfo;
	    					$tmpSuperAttribute = $superAttribute;
			    			$tmpSuperAttribute[$k] = $optionId;
			    			$superRequest['super_attribute'] = $tmpSuperAttribute;
			    			$superRequest['qty'] = $qty;

			    			// clone - Prevent duplicate cart item
			    			$superProduct = clone $product;

			    			$result = $proceed($superProduct, $superRequest);
			    		}
	    			}
	    		}
	    	} else {
	    		foreach ($pmtOptions as $k => $options) {
	    			$keys = explode('_', $k);
	    			foreach ($options as $optionId => $qty) {
	    				$keys1 = explode('_', $optionId);
	    				if ($qty) {
							$superAttribute                  = [];
							$superAttribute[$keys[0]]        = $keys1[0];
                            if (count($keys1) == 2) $superAttribute[$keys[1]] = $keys1[1];
							$superRequest['super_attribute'] = $superAttribute;
							$superRequest['qty']             = $qty;

			    			// clone - Prevent duplicate cart item
			    			$superProduct = clone $product;

			    			$result = $proceed($superProduct, $superRequest);
	    				}
	    			}
	    		}
	    	}

            if (!isset($result)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('You need to enter quantity for your item.'));
            }

            return $subject;

    	} else {
    		$result = $proceed($productInfo, $requestInfo);
    	}

    	return $result;
    }

    /**
     * Get product object based on requested product information
     *
     * @param   Product|int|string $productInfo
     * @return  Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
            }
        } else if (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'), $e);
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
        }
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t find the product.'));
        }
        return $product;
    }
}