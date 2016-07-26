<?php

use yii\db\Migration;
use yii\db\Schema;

class m160726_094751_add_table_product_vendor extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%product_vendor}}',
            [
                'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'vendor_id' => Schema::TYPE_INTEGER . ' NOT NULL ',
                'total' => Schema::TYPE_INTEGER . ' DEFAULT 0 '
            ]
        );

        $this->addForeignKey(
            'product_vendor_product_id',
            '{{%product_vendor}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'product_vendor_vendor_id',
            '{{%product_vendor}}',
            'vendor_id',
            '{{%vendor}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%product_vendor}}');
    }
}
