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

namespace Magezon\ProductMatrix\Plugin\Ui\DataProvider\Product\Form;

class ProductDataProvider
{
	/**
	 * @var \Magento\Framework\Stdlib\ArrayManager
	 */
	protected $arrayManager;

	/**
	 * @var \Magezon\ProductMatrix\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Framework\Stdlib\ArrayManager $arrayManager 
	 * @param \Magezon\ProductMatrix\Helper\Data     $dataHelper   
	 */
	public function __construct(
        \Magento\Framework\Stdlib\ArrayManager $arrayManager,
        \Magezon\ProductMatrix\Helper\Data $dataHelper
	) {
		$this->arrayManager = $arrayManager;
		$this->dataHelper   = $dataHelper;
	}

	public function afterGetMeta(
		\Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider $subject,
		$result
	) {
			
		if (!$this->dataHelper->isEnabled()) {
			$config = [
				'componentDisabled' => true
	        ];

			$path   = $this->arrayManager->findPath('enable_product_matrix', $result, null, 'children');
			$result = $this->arrayManager->merge($path . \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier::META_CONFIG_PATH, $result, $config);
		}

		return $result;
	}
}