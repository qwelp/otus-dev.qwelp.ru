<?php

namespace Otus\Iblock;

use Bitrix\Iblock\IblockTable;

class IblockUtils
{
    /**
     * Получает идентификатор инфоблока по его коду.
     *
     * @param string $code Код инфоблока
     * @return int|null Идентификатор инфоблока или null, если инфоблок не найден
     */
    public static function getIblockIdByCode(string $code): ?int
    {
        $arIblock = IblockTable::getList([
            'select' => ['ID'],
            'filter' => ['=CODE' => $code],
        ])->fetch();

        return $arIblock['ID'] ?? null;
    }
}
