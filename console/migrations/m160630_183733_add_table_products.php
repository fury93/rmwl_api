<?php

use yii\db\Migration;
use yii\db\Schema;

class m160630_183733_add_table_products extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%products}}',
            [
                'id' => Schema::TYPE_PK,
                'vendor_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'name' => Schema::TYPE_STRING . ' NOT NULL ',
                'code' => Schema::TYPE_INTEGER . ' NOT NULL',
                'description' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'status' => Schema::TYPE_STRING . ' NOT NULL ',
                'unit_of_measure' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'product_class' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'uom' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'cost' => Schema::TYPE_FLOAT . ' NOT NULL',
                'cost_per_unit' => Schema::TYPE_FLOAT . ' DEFAULT NULL',
                'price_per_unit' => Schema::TYPE_FLOAT . ' DEFAULT NULL',
                'image_path' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'effective_date' => Schema::TYPE_INTEGER . ' NOT NULL',
                'expiration_date' => Schema::TYPE_INTEGER . ' NOT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%products}}');
    }

}
