<?php

use yii\db\Migration;
use yii\db\Schema;

class m160715_095115_add_table_auth_item_user extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%auth_item_user}}',
            [
                'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'auth_item' => Schema::TYPE_STRING . '(64) NOT NULL ',
            ]
        );

        $this->addForeignKey(
            'auth_item_user_user_id',
            '{{%auth_item_user}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

/*        $this->addForeignKey(
            'auth_item_user_auth_item',
            '{{%auth_item_user}}',
            'auth_item',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );*/
    }

    public function down()
    {
        $this->dropTable('{{%auth_item_user}}');
    }
}
