<?php

use yii\db\Migration;
use yii\db\Schema;

class m160711_113749_add_table_patient extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%patient}}',
            [
                'user_id' => Schema::TYPE_INTEGER . ' NOT NULL UNIQUE',
                'last_name' => Schema::TYPE_STRING . ' NOT NULL ',
                'first_name' => Schema::TYPE_STRING . ' NOT NULL ',
                'middle_name' => Schema::TYPE_STRING . ' DEFAULT NULL ', //middle initial
                'date_birth' => Schema::TYPE_INTEGER . ' NOT NULL',
                'age' => Schema::TYPE_INTEGER . ' NOT NULL',
                'marital_status' => Schema::TYPE_STRING . ' NOT NULL ',
                'gender' => Schema::TYPE_STRING . ' NOT NULL ',
                'address' => Schema::TYPE_STRING . ' NOT NULL ',
                'city' => Schema::TYPE_STRING . ' NOT NULL ',
                'state' => Schema::TYPE_STRING . ' NOT NULL ',
                'zip_code' => Schema::TYPE_INTEGER . ' NOT NULL ',
                'cell_number' => Schema::TYPE_STRING . ' NOT NULL ',
                'home_number' => Schema::TYPE_STRING . ' NOT NULL ',
                'email' => Schema::TYPE_STRING . ' NOT NULL',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            ]
        );

        $this->addForeignKey(
            'patient_user_id',
            '{{%patient}}',
            'user_Id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropTable('{{%patient}}');
    }

}
