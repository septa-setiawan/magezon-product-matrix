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

namespace Magezon\ProductMatrix\Model\Config\Source;

class CustomerGroups implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository       
     * @param \Magento\Framework\Api\SearchCriteriaBuilder   $searchCriteriaBuilder 
     * @param \Magento\Framework\Convert\DataObject          $objectConverter       
     * @param array                                          $data                  
     */
    public function __construct(
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Convert\DataObject $objectConverter,
        array $data = []
    ) {
        $this->groupRepository        = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter       = $objectConverter;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups  = $this->groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems();
        $options = $this->_objectConverter->toOptionArray($groups, 'id', 'code');
        array_unshift($options, ['label' => __('ALL GROUPS'), 'value' => 'all']);
        return $options;
    }
}
