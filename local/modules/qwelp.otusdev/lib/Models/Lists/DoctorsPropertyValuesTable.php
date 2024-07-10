<?php

namespace Qwelp\Otusdev\Models\Lists;

use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Qwelp\Otusdev\Models\AbstractIblockPropertyValuesTable;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Qwelp\Otusdev\Models\Lists\ProceduresPropertyValuesTable;
use Bitrix\Main\ORM\Query\Join;

class DoctorsPropertyValuesTable extends AbstractIblockPropertyValuesTable
{
    const IBLOCK_ID = IBLOCK_ID_DOCTORS;

    public static function getMap(): array
    {
        $map['PROCEDURES'] = new ExpressionField(
            'PROCEDURES',
            sprintf('(select group_concat(e.ID, ";", e.NAME SEPARATOR "\0") as VALUE from %s as m join b_iblock_element as e on m.VALUE = e.ID where m.IBLOCK_ELEMENT_ID = %s and m.IBLOCK_PROPERTY_ID = %d)',
                static::getTableNameMulti(),
                '%s',
                static::getPropertyId('PROCEDURES_ID')
            ),
            ['IBLOCK_ELEMENT_ID'],
            ['fetch_data_modification' => [static::class, 'getMultipleFieldIdValueModifier']]
        );

        //$map['PROCEDURES_TEST'] = (new ManyToMany('PROCEDURES_TEST', ProceduresPropertyValuesTable::class))->configureTableName('b_iblock_element_prop_m16');

        $map['PROCEDURES_TEST'] = (new Reference(
            'PROCEDURES_TEST',
            ProceduresPropertyValuesTable::class,
            Join::on('this.PROCEDURES_ID', 'ref.IBLOCK_ELEMENT_ID')
        ))
        ->configureJoinType('inner');

        return parent::getMap() + $map;
    }
}
