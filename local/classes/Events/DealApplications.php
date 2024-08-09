<?php

namespace Otus\Events;

use Otus\Crm\Deal\DealApplicationUpdater;

/**
 * Класс для обработки событий, связанных с добавлением и обновлением сделок.
 */
class DealApplications
{
    /**
     * Обработка события после изменения сделки (добавление или обновление).
     *
     * @param array &$arDeal Поля сделки
     * @return void
     */
    private static function processDeal(array &$arDeal): void
    {
        if (empty($arDeal['ID'])) return;

        $dealUpdater = new DealApplicationUpdater($arDeal['ID']);
        $dealUpdater->updateDealApplication();
    }

    /**
     * Обработчик события после обновления сделки.
     *
     * @param array &$arDeal Поля сделки
     * @return void
     */
    public static function updateAfter(array &$arDeal): void
    {
        self::processDeal($arDeal);
    }

    /**
     * Обработчик события после добавления сделки.
     *
     * @param array &$arDeal Поля сделки
     * @return void
     */
    public static function addAfter(array &$arDeal): void
    {
        self::processDeal($arDeal);
    }

    /**
     * Удаляет заявку по идентификатору сделки.
     *
     * Метод создает экземпляр класса DealApplicationUpdater для указанного идентификатора сделки
     * и вызывает метод для удаления соответствующей заявки.
     *
     * @param int $dealId Идентификатор сделки
     * @return void
     */
    public static function deleteApplication(int $dealId): void
    {
        $dealUpdater = new DealApplicationUpdater($dealId);
        $dealUpdater->deleteApplication();
    }
}
