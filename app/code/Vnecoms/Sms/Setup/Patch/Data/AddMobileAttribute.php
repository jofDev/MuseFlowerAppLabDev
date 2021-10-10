<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnecoms\Sms\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Vnecoms\Sms\Model\ResourceModel\Mobile\CollectionFactory;

/**
 * Class add customer updated attribute to customer
 */
class AddMobileAttribute implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var \Vnecoms\Sms\Model\ResourceModel\Mobile\CollectionFactory
     */
    protected $mobileCollectionFactory;

    /**
     * AddMobileAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CustomerSetupFactory $customerSetupFactory
     * @param IndexerRegistry $indexerRegistry
     * @param CollectionFactory $mobileCollectionFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        IndexerRegistry $indexerRegistry,
        CollectionFactory $mobileCollectionFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->indexerRegistry = $indexerRegistry;
        $this->mobileCollectionFactory = $mobileCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $customerSetup->addAttribute(Customer::ENTITY, 'mobilenumber', [
            'type' => 'static',
            'label' => 'Mobile Number',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 100,
            'position' => 100,
            'used_in_grid' => true,
            'visible_in_grid' => true,
            'searchable_in_grid' => true,
            'filterable_in_grid' => true,
            'system' => 0,
        ]);
        $mobileNumberAttr = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'mobilenumber');
        $mobileNumberAttr->setData(
            'used_in_forms',
            ['adminhtml_customer', 'checkout_register', 'customer_account_create', 'adminhtml_checkout']
        )->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1);

        $mobileNumberAttr->setData('is_used_in_grid',true);
        $mobileNumberAttr->setData('is_visible_in_grid',true);
        $mobileNumberAttr->setData('is_filterable_in_grid',true);

        $mobileNumberAttr->save();


        /*Save exist mobile number from old table to customer mobile number attribute.*/
        $mobileCollection = $this->mobileCollectionFactory->create()
            ->addFieldToFilter('customer_id',['gt' => 0])
            ->addFieldToFilter('status', 1);

        foreach($mobileCollection as $mobile){
            $connection = $mobile->getResource()->getConnection();
            $connection->update(
                $mobileCollection->getTable('customer_entity'),
                ['mobilenumber' => $mobile->getMobile()],
                "entity_id = '{$mobile->getCustomerId()}'"
            );
        }

        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->invalidate();

        $entityTypeId = $customerSetup->getEntityTypeId(Customer::ENTITY);
        $customerSetup->addAttributeToSet(
            $entityTypeId,
            \Magento\Customer\Api\CustomerMetadataInterface::ATTRIBUTE_SET_ID_CUSTOMER,
            'General',
            'mobilenumber',
            null
        );

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getVersion()
    {
        return '2.0.9';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
