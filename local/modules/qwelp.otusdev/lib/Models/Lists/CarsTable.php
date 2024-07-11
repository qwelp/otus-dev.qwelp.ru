<?php

namespace Qwelp\Otusdev\Models\Lists;

use Bitrix\Main\Application;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;

use Bitrix\Main\ORM\Query\Join;

class CarsTable extends DataManager
{
    public static function getTableName()
    {
        return 'cars';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('NAME', [
                'required' => true,
            ])
        ];
    }

    public static function createTable(){
        $connection = Application::getInstance()->getConnection();

        if (!$connection->isTableExists(static::getTableName()))
        {
            static::getEntity()->createDbTable();
            return true;
        }
        else return false;
    }

    public static function dropTable()
    {
        $connection = Application::getConnection();

        if ($connection->isTableExists(static::getTableName())) {
            $connection->dropTable(static::getTableName());
        }
    }

    public static function insertDemoData()
    {
        $demoData = [
            ['NAME' => 'Toyota'],
            ['NAME' => 'Honda'],
            ['NAME' => 'Ford'],
            ['NAME' => 'Chevrolet'],
            ['NAME' => 'BMW'],
        ];

        foreach ($demoData as $data) {
            static::add($data);
        }
    }
}
