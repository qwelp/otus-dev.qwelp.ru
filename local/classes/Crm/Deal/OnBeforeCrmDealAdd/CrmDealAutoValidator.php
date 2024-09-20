<?php

namespace Otus\Crm\Deal\OnBeforeCrmDealAdd;

use Bitrix\Crm\DealTable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;

/**
 * Класс CrmDealAutoValidator
 *
 * Выполняет проверку на наличие незакрытых сделок по одному и тому же автомобилю перед созданием новой сделки.
 * Если найдены незакрытые сделки, создается уведомление для ответственного пользователя и отменяется создание новой сделки.
 */
class CrmDealAutoValidator
{
    /**
     * Метод onBeforeDealSave
     *
     * Выполняет валидацию перед созданием новой сделки. Проверяет наличие незакрытых заказов по тому же автомобилю.
     * Если есть незакрытые сделки, отправляет уведомление ответственному и отменяет сохранение сделки.
     *
     * @param array &$arFields Массив полей сделки, передаваемый по ссылке.
     * @return bool Возвращает true, если сделку можно сохранить, или false, если сохранение отменено.
     */
    public static function onBeforeDealSave(&$arFields)
    {
        // Проверяем наличие кастомного поля автомобиля
        $ufClientAuto = $arFields['UF_CLIENT_AUTO'];

        if ($ufClientAuto) {
            // Проверяем количество незакрытых сделок с тем же автомобилем
            $countNotCloseDeals = DealTable::getList([
                'select' => ['ID'],
                'filter' => [
                    'CLOSED' => 'N',
                    'UF_CLIENT_AUTO' => $ufClientAuto,
                ],
            ])->getSelectedRowsCount();

            if ($countNotCloseDeals > 0) {
                $responsibleId = $arFields['ASSIGNED_BY_ID'];

                // Отправляем уведомление через мессенджер Bitrix ответственному
                \CIMNotify::Add([
                    "MESSAGE_TYPE" => IM_MESSAGE_SYSTEM,
                    "TO_USER_ID" => $responsibleId,
                    "FROM_USER_ID" => CurrentUser::get()->getId(),
                    "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                    "NOTIFY_MODULE" => "crm",
                    "NOTIFY_EVENT" => "deal_update",
                    "NOTIFY_TAG" => "CRM|DEAL|" . $arFields['ID'],
                    "NOTIFY_MESSAGE" => Loc::getMessage('OTUS_CRM_DEAL_AUTO_VALIDATOR_NOTIFY_MESSAGE'),
                ]);

                $arFields['RESULT_MESSAGE'] = Loc::getMessage('OTUS_CRM_DEAL_AUTO_VALIDATOR_NOTIFY_MESSAGE');

                return false;
            }
        }

        return true;
    }
}
