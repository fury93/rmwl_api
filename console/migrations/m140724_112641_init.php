<?php

use yii\db\Schema;
use yii\db\Migration;
use rest\versions\v1\models\User;

class m140724_112641_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%user}}',
            [
                'id' => Schema::TYPE_PK,
                'username' => Schema::TYPE_STRING . ' NOT NULL ',
                'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
                'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
                'password_reset_token' => Schema::TYPE_STRING,
                'email' => Schema::TYPE_STRING . ' NOT NULL',
                'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT ' . User::STATUS_ACTIVE,
                'role' => Schema::TYPE_STRING . ' NOT NULL DEFAULT "' . User::ROLE_PATIENT . '"',
                'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
                'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            ],
            $tableOptions
        );

        $user = new User();
        $user->username = 'demo';
        $user->email = 'demo@mail.net';
        $user->generateAuthKey();
        $user->setPassword('demo');
        $user->role = User::ROLE_ADMIN;
        return $user->insert();
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
