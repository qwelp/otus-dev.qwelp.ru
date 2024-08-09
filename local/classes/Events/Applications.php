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
     * @param array &$arApplication Поля элемента инфоблока
     * @return void
     */
    public static function updateAfter(array &$arApplication): void
    {
        if ($arApplication['IBLOCK_ID'] != IBLOCK_ID_OTUS_APPLICATIONS) return;

        $applicationId = $arApplication['ID'];
        $dealUpdater = new DealUpdater($applicationId);
        $dealUpdater->updateDeal();
    }

    /**
     * Обработчик события после добавления элемента в инфоблок.
     *
     * @param array &$arApplication Поля элемента инфоблока
     * @return void
     */
    public static function addAfter(array &$arApplication): void
    {
        if ($arApplication['IBLOCK_ID'] != IBLOCK_ID_OTUS_APPLICATIONS) return;

        $applicationId = $arApplication['ID'];
        $dealUpdater = new DealUpdater($applicationId);
        $dealUpdater->updateDeal();
    }

    /**
     * Обработчик события удаления сделки при удалении заявки.
     *
     * Метод создает экземпляр класса DealUpdater для указанного идентификатора заявки
     * и вызывает метод для удаления соответствующей сделки.
     *
     * @param int $applicationId Идентификатор заявки
     * @return void
     */
    public static function deleteDeal(int $applicationId): void
    {
        $dealUpdater = new DealUpdater($applicationId);
        $dealUpdater->deleteDeal();
    }
}
