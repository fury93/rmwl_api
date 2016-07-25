<?php

use yii\db\Migration;
use yii\db\Schema;
use \rest\versions\v1\models\Vendor;

class m160723_143708_add_table_vendor extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%vendor}}',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING . ' NOT NULL',
                'address' => Schema::TYPE_STRING . ' NOT NULL ',
                'description' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'contact_info' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'status' => Schema::TYPE_STRING . ' NOT NULL DEFAULT "' . Vendor::VENDOR_STATUS_ACTIVE . '"',
                'image_path' => Schema::TYPE_STRING . ' DEFAULT NULL ',
                'start_date' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'end_date' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%vendor}}');
    }
}
