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

namespace Magezon\ProductMatrix\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class AddEnableProductMatrixAttribute implements
    DataPatchInterface,
    PatchVersionInterface
{
    /**
     * EAV setup factory class
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }
    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
    /**
     * @inheritDoc
     */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Add enable_product_matrix attribute to the 'eav_attribute' table
         */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'enable_product_matrix',
            [
                'group'                      => 'Product Details',
                'sort_order'                 => 200,
                'type'                       => 'int',
                'backend'                    => '',
                'label'                      => 'Enable Matrix Table',
                'input'                      => 'boolean',
                'class'                      => '',
                'source'                     => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'global'                 => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible'                    => true,
                'required'                   => false,
                'user_defined'               => false,
                'default'                    => 1,
                'searchable'                 => false,
                'filterable'                 => false,
                'comparable'                 => false,
                'visible_on_front'           => false,
                'visible_in_advanced_search' => false,
                'used_in_product_listing'    => false,
                'unique'                     => false,
                'is_used_in_grid'            => false,
                'is_visible_in_grid'         => false,
                'is_filterable_in_grid'      => false,
                'apply_to'                   =>'configurable'
            ]
        );
    }
    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.0.0';
    }
}
