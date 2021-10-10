<?php
namespace Applab\Subscription\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetup
     */
    private $salesSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param QuoteSetupFactory $quoteSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var QuoteSetup $quoteSetup */
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);

        /** @var SalesSetup $salesSetup */
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */
        /*$eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'customer_comment',
            [
                'type'                    => 'text',
                'label'                   => 'Customer Comment',
                'input'                   => 'textarea',
                'global'                  => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => true,
                'default'                 => '',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => true,
                'used_in_product_listing' => false,
                'unique'                  => false,
                'wysiwyg_enabled'         => false
            ]
        );

        $attributeSetId = $eavSetup->getDefaultAttributeSetId('catalog_product');
        $eavSetup->addAttributeToSet(
            'catalog_product',
            $attributeSetId,
            'General',
            'customer_comment'
        );*/

        $quoteSetup->addAttribute('quote_item', 'delivery_date',[]);
        $salesSetup->addAttribute('order_item', 'delivery_date',[]);
        $quoteSetup->addAttribute('quote_item', 'delivery_time',[]);
        $salesSetup->addAttribute('order_item', 'delivery_time',[]);
        $quoteSetup->addAttribute('quote_item', 'gift_qrcode',[]);
        $salesSetup->addAttribute('order_item', 'gift_qrcode',[]);
    }
}