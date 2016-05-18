<?php
/**
* Copyright © Pulse Storm LLC 2016
* All rights reserved
*/
namespace Austinpeterson\Commercebug\Setup;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //START: install stuff
        //END:   install stuff
        
//START table setup
$table = $installer->getConnection()->newTable(
            $installer->getTable('pulsestorm_commercebug_log')
    )->addColumn(
            'pulsestorm_commercebug_log_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array (
  'identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,
),
            'Entity ID'
        )->addColumn(
            'json_log',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            array (
  'nullable' => false,
),
            'Demo Title'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            array (
  'nullable' => false,'default' => '1',
),
            'Is Active'
        );
$installer->getConnection()->createTable($table);
//END   table setup
$installer->endSetup();
    }
}
