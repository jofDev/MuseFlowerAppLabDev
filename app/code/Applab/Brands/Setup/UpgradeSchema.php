<?php
namespace Applab\Brands\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;


class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    { 
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('ves_brand'),
                'brand_map_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'brand_map_id',
                    'after' => 'status'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('ves_brand'),
                'name_ar',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'comment' => 'name_ar',
                    'after' => 'name'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('ves_brand'),
                'description_ar',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'description_ar',
                    'after' => 'description'
                ]
            );
        }
        $installer->endSetup();
    }
}
