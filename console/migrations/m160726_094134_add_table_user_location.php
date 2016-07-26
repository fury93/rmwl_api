<?php

use yii\db\Migration;
use yii\db\Schema;

class m160726_094134_add_table_user_location extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%user_location}}',
            [
                'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'location_id' => Schema::TYPE_INTEGER . ' NOT NULL ',
            ]
        );

        $this->addForeignKey(
            'user_location_user_id',
            '{{%user_location}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_location_location_id',
            '{{%user_location}}',
            'location_id',
            '{{%location}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%user_location}}');
    }
}
