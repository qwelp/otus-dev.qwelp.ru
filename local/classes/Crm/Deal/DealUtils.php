<?php

namespace Otus\Crm\Deal;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;

class DealUtils {
    /**
     * Проверяет, прикреплен ли клиент (контакт или компания) к сделке и возвращает ID клиента, если он есть.
     *
     * @param int $dealId ID сделки.
     *
     * @return int|null Возвращает ID клиента (контакт или компания), если он прикреплен, или null, если клиент не найден.
     */
    public static function getClientIdFromDeal($dealId): ?int
    {
        Loader::includeModule('crm');

        // Получаем данные о сделке
        $deal = DealTable::getList([
            'select' => ['CONTACT_ID', 'COMPANY_ID'],
            'filter' => ['ID' => $dealId],
        ])->fetch();

        // Проверяем, есть ли прикрепленный контакт
        if (!empty($deal['CONTACT_ID'])) {
            return (int)$deal['CONTACT_ID']; // Возвращаем ID контакта
        }

        return null; // Клиент не прикреплен
    }
}