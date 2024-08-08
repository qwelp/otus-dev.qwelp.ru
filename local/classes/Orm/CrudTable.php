<?php

namespace Otus\Orm;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

class CrudTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'otus_test_crud';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID',
                []
            ))->configureTitle(Loc::getMessage('CRUD_ENTITY_ID_FIELD'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
            ,
            'ENTITY_ID' => (new StringField('ENTITY_ID',
                [
                    'validation' => function () {
                        return [
                            new LengthValidator(null, 255),
                        ];
                    },
                ]
            ))->configureTitle(Loc::getMessage('CRUD_ENTITY_ENTITY_ID_FIELD'))
                ->configureRequired(true)
            ,
            'ELEMENT_ID' => (new IntegerField('ELEMENT_ID',
                []
            ))->configureTitle(Loc::getMessage('CRUD_ENTITY_ELEMENT_ID_FIELD'))
                ->configureRequired(true)
            ,
        ];
    }
}
