<?php

namespace Mijora\Venipak\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            if ($installer->getConnection()->tableColumnExists('venipak_order', 'delivery_status') === false) {
                $installer->getConnection()->addColumn(
                        $installer->getTable('venipak_order'),
                        'delivery_status',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => 'Delivery status',
                            'length' => '255',
                        ]
                );
            }
        }
        $setup->endSetup();
    }

}
