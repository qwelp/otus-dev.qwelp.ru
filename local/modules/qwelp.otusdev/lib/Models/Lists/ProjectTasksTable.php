<?php

namespace Qwelp\Otusdev\Models\Lists;

use Bitrix\Main\Application;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;

use Bitrix\Main\ORM\Query\Join;

class ProjectTasksTable extends DataManager
{
    public static function getTableName()
    {
        return 'project_tasks';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('TASK_NAME', [
                'required' => true,
            ]),

            new IntegerField('PROJECT_ID', [
                'required' => true,
            ]),
            (new Reference('PROJECT', \Bitrix\Iblock\Elements\ElementProjectsTable::class,
                Join::on('this.PROJECT_ID', 'ref.ID')))->configureJoinType('inner'),

            new IntegerField('TASK_ID', [
                'required' => true,
            ]),
            (new Reference('TASK', \Bitrix\Iblock\Elements\ElementTasksTable::class,
                Join::on('this.TASK_ID', 'ref.ID')))->configureJoinType('inner'),

            new IntegerField('EMPLOYEE_ID', [
                'required' => true,
            ]),
            (new Reference('EMPLOYEE', \Bitrix\Iblock\Elements\ElementEmployeesTable::class,
                Join::on('this.EMPLOYEE_ID', 'ref.ID')))->configureJoinType('inner'),

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
}
