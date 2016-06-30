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
                'name' => Schema::TYPE_STRING . ' NOT NULL ',
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
