<?php

namespace Mijora\Venipak\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
        $installer = $setup;
        $installer->startSetup();

        if ($installer->getConnection()->tableColumnExists('quote_address', 'venipak_data') === false) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('quote_address'),
                    'venipak_data',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Venipak data',
                    ]
            );
        }

        if ($installer->getConnection()->tableColumnExists('sales_order_address', 'venipak_data') === false) {
            $installer->getConnection()->addColumn(
                    $installer->getTable('sales_order_address'),
                    'venipak_data',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Venipak data',
                    ]
            );
        }

        if (!$installer->tableExists('venipak_warehouse')) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('venipak_warehouse')
                    )
                    ->addColumn(
                            'warehouse_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [
                                'identity' => true,
                                'nullable' => false,
                                'primary' => true,
                                'unsigned' => true,
                            ],
                            'Warehouse ID'
                    )
                    ->addColumn(
                            'name',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            ['nullable => false'],
                            'Warehouse name'
                    )
                    ->addColumn(
                            'contact_name',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Contact name'
                    )
                    ->addColumn(
                            'address',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Address'
                    )
                    ->addColumn(
                            'postcode',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Postcode'
                    )
                    ->addColumn(
                            'city',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'City'
                    )
                    ->addColumn(
                            'country',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Country'
                    )
                    ->addColumn(
                            'phone',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Phone'
                    )
                    ->addColumn(
                            'company_code',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Company code'
                    )
                    ->addColumn(
                            'default',
                            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                            1,
                            ['default' => 0],
                            'Default warehouse'
                    )
                    ->addColumn(
                            'created_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                            'Created At'
                    )->addColumn(
                            'updated_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                            'Updated At')
                    ->setComment('Venipak warehouses');
            $installer->getConnection()->createTable($table);

        }
        
        if (!$installer->tableExists('venipak_manifest')) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('venipak_manifest')
                    )
                    ->addColumn(
                            'manifest_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [
                                'identity' => true,
                                'nullable' => false,
                                'primary' => true,
                                'unsigned' => true,
                            ],
                            'Manifest ID'
                    )
                    ->addColumn(
                            'manifest_number',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Manifest number'
                    )
                    ->addColumn(
                            'warehouse_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [],
                            'Warehouse id'
                    )
                    ->addColumn(
                            'weight',
                            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                            null,
                            [],
                            'Total weight'
                    )
                    ->addColumn(
                            'is_closed',
                            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                            1,
                            ['default' => 0],
                            'Is closed'
                    )
                    ->addColumn(
                            'comment',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Manifest comment'
                    )
                    ->addColumn(
                            'arrival_date_from',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => true],
                            'Arrival date from'
                    )
                    ->addColumn(
                            'arrival_date_to',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => true],
                            'Arrival date to'
                    )
                    ->addColumn(
                            'created_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                            'Created At'
                    )->addColumn(
                            'updated_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                            'Updated At')
                    ->setComment('Venipak manifests');
            $installer->getConnection()->createTable($table);

        }
        
        if (!$installer->tableExists('venipak_order')) {
            $table = $installer->getConnection()->newTable(
                            $installer->getTable('venipak_order')
                    )
                    ->addColumn(
                            'id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [
                                'identity' => true,
                                'nullable' => false,
                                'primary' => true,
                                'unsigned' => true,
                            ],
                            'ID'
                    )
                    ->addColumn(
                            'order_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            ['unsigned' => true],
                            'Order id'
                    )
                    ->addColumn(
                            'warehouse_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            ['unsigned' => true],
                            'Warehouse id'
                    )
                    ->addColumn(
                            'manifest_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            ['unsigned' => true],
                            'Manifest id'
                    )
                    ->addColumn(
                            'number_of_packages',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [],
                            'Number of packages'
                    )
                    ->addColumn(
                            'weight',
                            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                            null,
                            [],
                            'Weight'
                    )
                    ->addColumn(
                            'is_cod',
                            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                            1,
                            ['default' => 0],
                            'Is cod'
                    )
                    ->addColumn(
                            'cod_amount',
                            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                            null,
                            [],
                            'COD amount'
                    )
                    ->addColumn(
                            'delivery_time',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Delivery time'
                    )
                    ->addColumn(
                            'door_code',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Door code'
                    )
                    ->addColumn(
                            'cabinet_number',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Cabinet number'
                    )
                    ->addColumn(
                            'warehouse_number',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Warehouse number'
                    )
                    ->addColumn(
                            'call_before_delivery',
                            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                            1,
                            ['default' => 0],
                            'Call before delivery'
                    )
                    ->addColumn(
                            'return_signed_document',
                            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                            1,
                            ['default' => 0],
                            'Return signed document'
                    )
                    ->addColumn(
                            'label_generated_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => true],
                            'Labels generated at'
                    )
                    ->addColumn(
                            'label_number',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Label number'
                    )
                    ->addColumn(
                            'created_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                            'Created At'
                    )->addColumn(
                            'updated_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                            'Updated At')
                    ->setComment('Venipak manifests');
            $installer->getConnection()->createTable($table);

        }


        $setup->endSetup();
    }

}
