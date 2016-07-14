<?php

namespace rest\versions\v1\models;

use Yii;

class Role{
    const ROLE_ADMIN = 'Admin'; //super user
    const ROLE_PATIENT = 'Patient'; // New patient and returning patient
    const ROLE_ENTRY = 'Entry';
    const ROLE_MANAGEMENT = 'Management';
    const ROLE_INVENTORY_MANAGEMENT = 'Inventory Management';
    const ROLE_GUEST = 'Guest';

    public static function getRolesList()
    {
        return [
            self::ROLE_ADMIN => self::ROLE_ADMIN,
            self::ROLE_PATIENT => self::ROLE_PATIENT,
            self::ROLE_ENTRY => self::ROLE_ENTRY,
            self::ROLE_MANAGEMENT => self::ROLE_MANAGEMENT,
            self::ROLE_INVENTORY_MANAGEMENT => self::ROLE_INVENTORY_MANAGEMENT
        ];
    }
}