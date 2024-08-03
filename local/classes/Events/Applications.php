<?php

namespace Otus\Events;

use Otus\Crm\Deal\DealUpdater;

/**
 * Класс для обработки событий, связанных с инфоблоком заявок.
 */
class Applications
{
    /**
     * Обработчик события после обновления элемента инфоблока.
     *
     * @param array &$fields Поля элемента инфоблока
     * @return void
     */
    public static function updateAfter(array &$fields): void
    {
        if ($fields['IBLOCK_ID'] == IBLOCK_ID_OTUS_APPLICATIONS) {
            $applicationId = $fields['ID'];
            $dealUpdater = new DealUpdater($applicationId);
            $dealUpdater->updateDeal();
        }
    }

    /**
     * Обработчик события после добавления элемента в инфоблок.
     *
     * @param array &$fields Поля элемента инфоблока
     * @return void
     */
    public static function addAfter(array &$fields): void
    {
        if ($fields['IBLOCK_ID'] == IBLOCK_ID_OTUS_APPLICATIONS) {
            $applicationId = $fields['ID'];
            $dealUpdater = new DealUpdater($applicationId);
            $dealUpdater->updateDeal();
        }
    }
}
