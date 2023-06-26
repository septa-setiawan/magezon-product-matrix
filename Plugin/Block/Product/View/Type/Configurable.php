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

namespace Magezon\ProductMatrix\Plugin\Block\Product\View\Type;

use Magento\Catalog\Model\Product;

class Configurable
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @var \Magento\Framework\Json\EncoderInterface
	 */
	protected $jsonEncoder;

	/**
	 * @var \Magento\Framework\Json\DecoderInterface
	 */
	protected $jsonDecoder;

	/**
	 * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
	 */
	protected $_stockItemRepository;

	/**
	 * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
	 */
	protected $stockConfiguration;

	/**
	 * @var \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface
	 */
	protected $stockRegistryProvider;

	/**
	 * @var \Magezon\ProductMatrix\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\Registry                                        $registry              
	 * @param \Magento\Framework\Json\EncoderInterface                           $jsonEncoder           
	 * @param \Magento\Framework\Json\DecoderInterface                           $jsonDecoder           
	 * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository          $stockItemRepository   
	 * @param \Magento\CatalogInventory\Api\StockConfigurationInterface          $stockConfiguration    
	 * @param \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider 
	 * @param \Magezon\ProductMatrix\Helper\Data                                 $dataHelper            
	 */
    public function __construct(
        \Magento\Framework\Registry $registry,
    	\Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
		\Magezon\ProductMatrix\Helper\Data $dataHelper
    ) {
		$this->_coreRegistry         = $registry;
		$this->jsonEncoder           = $jsonEncoder;
		$this->jsonDecoder           = $jsonDecoder;
		$this->_stockItemRepository  = $stockItemRepository;
		$this->stockConfiguration    = $stockConfiguration;
		$this->stockRegistryProvider = $stockRegistryProvider;
		$this->dataHelper            = $dataHelper;
    }

    public function beforeGetAllowProducts(
    	\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
    ) {
    	if (!$subject->hasAllowProducts() && $this->dataHelper->isEnabled()) {
            $products = [];
            $this->_coreRegistry->register('mgz-productmatrix', 1);
            $allProducts = $subject->getProduct()->getTypeInstance()->getUsedProducts($subject->getProduct(), null);
            $this->_coreRegistry->unRegister('mgz-productmatrix');
            foreach ($allProducts as $product) {
                $products[] = $product;
            }
            $subject->setAllowProducts($products);
        }
    }

    /**
     * Assign stock status information to product
     *
     * @param Product $product
     * @param int $status
     * @return void
     */
    public function checkIsSalable(Product $product)
    {
		$scopeId     = $this->stockConfiguration->getDefaultScopeId();
		$stockStatus = $this->stockRegistryProvider->getStockStatus($product->getId(), $scopeId);
		return $stockStatus->getStockStatus();
    }

	public function afterGetJsonConfig(
		\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
		$result
	) {
		if ($this->dataHelper->isEnabled()) {
			$allowProducts = $subject->getAllowProducts();
			$result = $this->jsonDecoder->decode($result); 
			foreach ($result['optionPrices'] as $productId => &$_row) {
				$product = false;

				foreach ($allowProducts as $_product) {
					if ($_product->getId() == $productId) {
						$product = $_product;
						break;
					}
				}

				if ($product) {
					$_row['qty']       = $this->_stockItemRepository->get($productId)->getQty();
					$_row['isSalable'] = $this->checkIsSalable($product) ? true : false;
					$_row['id']        = (int)$product->getId();
				}
			}
			$result['enableProductMattrix'] = $subject->getProduct()->hasData('enable_product_mattrix') ? !!$subject->getProduct()->getEnableProductMatrix() : true;
			$result = $this->jsonEncoder->encode($result);
		}

		return $result;
	}
}