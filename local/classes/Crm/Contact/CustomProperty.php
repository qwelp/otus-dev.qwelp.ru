<?php

namespace Otus\Crm\Contact;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

class CustomProperty
{
    /**
     * Обновляет пользовательское свойство "Дата последнего контакта" для контакта.
     *
     * Метод получает данные активности по её идентификатору и обновляет
     * пользовательское свойство контакта, если активность является email или звонком.
     *
     * @param int $activityId Идентификатор активности
     * @return void
     */
    public static function updateDateLastCommunication(int $activityId): void
    {
        Loader::includeModule('crm');

        $arActivity = \Bitrix\Crm\ActivityTable::getList([
            'select' => ['START_TIME', 'OWNER_ID', 'PROVIDER_TYPE_ID'],
            'filter' => ['ID' => $activityId]
        ])->fetch();

        if (!$arActivity) {
            self::logEvent(
                Loc::getMessage('OTUS_CRM_CONTACT_ACTIVITY_NOT_FOUND', ['#ACTIVITY_ID#' => $activityId]),
                \CEventLog::SEVERITY_ERROR
            );
            return;
        }

        if (in_array($arActivity['PROVIDER_TYPE_ID'], ['TODO', 'CALL'])) {
            $contactFields = [
                'UF_CRM_1722681948' => $arActivity['START_TIME']
            ];

            $contactEntity = new \CCrmContact(false);
            $result = $contactEntity->Update($arActivity['OWNER_ID'], $contactFields);

            if ($result) {
                self::logEvent(
                    Loc::getMessage('OTUS_CRM_CONTACT_UPDATE_SUCCESS', ['#CONTACT_ID#' => $arActivity['OWNER_ID']]),
                    \CEventLog::SEVERITY_INFO
                );
            } else {
                self::logEvent(
                    Loc::getMessage('OTUS_CRM_CONTACT_UPDATE_ERROR', ['#CONTACT_ID#' => $arActivity['OWNER_ID']]),
                    \CEventLog::SEVERITY_ERROR
                );
            }
        } else {
            self::logEvent(
                Loc::getMessage('OTUS_CRM_CONTACT_WRONG_ACTIVITY_TYPE', ['#ACTIVITY_ID#' => $activityId]),
                \CEventLog::SEVERITY_WARNING
            );
        }
    }

    /**
     * Логирует событие в журнал Битрикса.
     *
     * @param string $message Сообщение для логирования
     * @param string $severity Уровень важности (INFO, WARNING, ERROR)
     * @return void
     */
    private static function logEvent(string $message, string $severity): void
    {
        \CEventLog::Add([
            "SEVERITY" => $severity,
            "AUDIT_TYPE_ID" => "CONTACT_UPDATE",
            "MODULE_ID" => "crm",
            "ITEM_ID" => '',
            "DESCRIPTION" => $message
        ]);
    }
}
