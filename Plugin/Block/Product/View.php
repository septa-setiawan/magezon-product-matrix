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

namespace Magezon\ProductMatrix\Plugin\Block\Product;

class View
{
	/**
	 * @var \Magezon\ProductMatrix\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magezon\ProductMatrix\Helper\Data $dataHelper 
	 */
	public function __construct(
		\Magezon\ProductMatrix\Helper\Data $dataHelper
	) {
		$this->dataHelper = $dataHelper;
	}

	public function aroundShouldRenderQuantity(
		\Magento\Catalog\Block\Product\View $subject,
		\Closure $proceed
	) {
		$product = $subject->getProduct();

		if ($this->dataHelper->isEnabled() && ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)) {
			return false;
		}
		return $proceed();
	}
}