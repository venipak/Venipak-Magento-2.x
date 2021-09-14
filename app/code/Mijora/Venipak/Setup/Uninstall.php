<?php

namespace Mijora\Venipak\Setup;
 
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
 
class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
 
        $setup->getConnection()->dropColumn($setup->getTable('quote_address'), 'venipak_pickup_point');
        $setup->getConnection()->dropColumn($setup->getTable('sales_order_address'), 'venipak_pickup_point');
 
        $setup->endSetup();
    }
}