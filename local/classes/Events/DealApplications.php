<?php

namespace Otus\Events;

use Otus\Crm\Deal\DealApplicationUpdater;

/**
 * Класс для обработки событий, связанных с добавлением и обновлением сделок.
 */
class DealApplications
{
    /**
     * Обработчик события после обновления сделки.
     *
     * @param array &$fields Поля сделки
     * @return void
     */
    public static function updateAfter(array &$fields): void
    {
        if (!empty($fields['ID'])) {
            $dealUpdater = new DealApplicationUpdater($fields['ID']);
            $dealUpdater->updateDealApplication();
        }
    }

    /**
     * Обработчик события после добавления сделки.
     *
     * @param array &$fields Поля сделки
     * @return void
     */
    public static function addAfter(array &$fields): void
    {
        if (!empty($fields['ID'])) {
            $dealUpdater = new DealApplicationUpdater($fields['ID']);
            $dealUpdater->updateDealApplication();
        }
    }
}
