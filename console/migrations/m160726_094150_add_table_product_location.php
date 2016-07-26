<?php

use yii\db\Migration;
use yii\db\Schema;

class m160726_094150_add_table_product_location extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%product_location}}',
            [
                'product_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'location_id' => Schema::TYPE_INTEGER . ' NOT NULL ',
            ]
        );

        $this->addForeignKey(
            'product_location_product_id',
            '{{%product_location}}',
            'product_id',
            '{{%product}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'product_location_location_id',
            '{{%product_location}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%product_location}}');
    }
}
