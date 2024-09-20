<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Iblock\Elements\ElementGarageTable;
use Otus\Crm\Deal\DealUtils;
use Bitrix\Crm\DealTable;
use Bitrix\Main\Loader;

class ClientAutoManagerComponent extends CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [
            'getClientAutoById' => [
                'prefilters' => [],
            ],
            'isAutoOwnedByClient' => [
                'prefilters' => [],
            ],
            ' getDealAutoById' => [
                'prefilters' => [],
            ],
        ];
    }

    /**
     * Возвращает информацию о сделках, связанных с автомобилем по ID, включая товары.
     *
     * @param array $post Массив данных, включающий ID автомобиля.
     *
     * @return array Список сделок с информацией о товарах и дате создания.
     */
    public function getDealAutoByIdAction(array $post): array
    {
        Loader::includeModule('crm');

        // Получаем ID автомобиля из POST-запроса
        $autoId = $post['autoId'];

        // Получаем список сделок, связанных с автомобилем
        $deals = \Bitrix\Crm\DealTable::getList([
            'select' => ['ID', 'TITLE', 'DATE_CREATE', 'UF_CLIENT_AUTO'],
            'filter' => ['UF_CLIENT_AUTO' => $autoId],
        ])->fetchAll();

        $result = [];

        // Обрабатываем каждую сделку
        foreach ($deals as $deal) {
            $dealId = $deal['ID'];

            // Получаем товары, связанные с конкретной сделкой
            $productRows = \CCrmDeal::LoadProductRows($dealId);
            $products = array_map(function ($productRow) {
                return [
                    'NAME' => $productRow['PRODUCT_NAME'],
                    'QUANTITY' => $productRow['QUANTITY'],
                    'PRICE' => $productRow['PRICE'],
                ];
            }, $productRows);

            // Форматируем дату создания сделки
            $dateFormatted = FormatDate("j F Y", $deal['DATE_CREATE']->getTimestamp());

            // Добавляем результат по сделке в итоговый массив
            $result[] = [
                'ID' => $dealId,
                'TITLE' => $deal['TITLE'],
                'DATE_CREATE' => $dateFormatted,
                'PRODUCTS' => $products,
            ];
        }

        return $result;
    }

    /**
     * Проверяет, принадлежит ли автомобиль указанному клиенту.
     *
     * @param array $post Массив данных, содержащий 'autoId' и 'clientId'.
     *
     * @return bool Возвращает true, если автомобиль принадлежит клиенту, иначе false.
     */
    public static function isAutoOwnedByClientAction($post)
    {
        $dealId = $post['dealId'];
        $autoId = $post['clientAutoId'];
        $clientId = $post['contactId'];

        $isAutoOwned = ElementGarageTable::getList([
            'select' => [
                'ID',
                'KLIENT_ID' => 'KLIENT.VALUE',
            ],
            'filter' => [
                'ID' => $autoId,
                'KLIENT_ID' => $clientId,
            ],
        ])->fetch();

        if ($isAutoOwned) {
            return true;
        }

        global $USER_FIELD_MANAGER;
        $USER_FIELD_MANAGER->Update("CRM_DEAL", $dealId, ['UF_CLIENT_AUTO' => '']);
        return false;
    }

    function executeComponent() {}

    public function saveAutoAction($post)
    {
        global $USER_FIELD_MANAGER;

        [$dealId, $autoId] = $post;

        $USER_FIELD_MANAGER->Update("CRM_DEAL", $dealId, ['UF_CLIENT_AUTO' => $autoId]);

        return [$dealId, $autoId];
    }

    public function getClientIdAction($post): int
    {
        return DealUtils::getClientIdFromDeal($post['dealId']);
    }

    public function getClientAutoByIdAction($post): array
    {
        //global $USER_FIELD_MANAGER;
        //$clientAutoId = $USER_FIELD_MANAGER->GetUserFieldValue('CRM_DEAL', 'UF_CLIENT_AUTO', 29);

        $autoId = $post['autoId'];

        return ElementGarageTable::getList([
            'select' => [
                'ID',
                'NAME' => 'NAME',
                'MODEL_VALUE' => 'MODEL.VALUE',
                'YEAR_VALUE' => 'YEAR.VALUE',
                'TSVET_VALUE' => 'TSVET.VALUE',
                'PROBEG_VALUE' => 'PROBEG.VALUE',
                'KLIENT_ID' => 'KLIENT.VALUE',
            ],
            'filter' => [
                'ID' => $autoId,
            ],
        ])->fetch();
    }
}
