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

namespace Magezon\ProductMatrix\Plugin\Helper;

class Stock
{
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param \Magento\Framework\Registry $registry
	 */
	public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
    }

	public function beforeAddIsInStockFilterToCollection(
		\Magento\CatalogInventory\Helper\Stock $subject,
		$collection
	) {
		if ($this->_coreRegistry->registry('mgz-productmatrix')) {
			$collection->setFlag('has_stock_status_filter', true);
		}
	}
}