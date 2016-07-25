<?php

use yii\db\Migration;
use yii\db\Schema;

class m160722_084921_add_table_locations extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%location}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'address' => Schema::TYPE_STRING . ' NOT NULL ',
                'description' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'lat' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'lon' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'cell_number' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%location}}');
    }
}
