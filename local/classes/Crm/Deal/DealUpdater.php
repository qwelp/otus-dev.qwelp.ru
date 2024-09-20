<?php

namespace Otus\Crm\Deal;

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Loader;
use Bitrix\Iblock\Elements\ElementApplicationsTable;
use Bitrix\Crm\DealTable;
use Bitrix\Crm\Binding\DealContactTable;
use \Bitrix\Main\Localization\Loc;

/**
 * Класс для обновления сделки на основе данных из инфоблока.
 */
class DealUpdater
{
    /**
     * @var int ID заявки
     */
    private $applicationId;

    /**
     * Конструктор класса DealUpdater.
     *
     * @param int $applicationId ID заявки
     */
    public function __construct(int $applicationId)
    {
        $this->applicationId = $applicationId;
        $this->includeModules();
    }

    /**
     * Загружает необходимые модули.
     *
     * @return void
     */
    private function includeModules(): void
    {
        if (!Loader::includeModule('iblock') || !Loader::includeModule('crm')) {
            throw new \Exception(Loc::getMessage('OTUS_IBLOCK_MODULE_LOAD_ERROR'));
        }
    }

    /**
     * Выполняет обновление сделки на основе данных заявки.
     *
     * @return bool
     */
    public function updateDeal(): bool
    {
        $applicationData = $this->getApplicationData();

        if ($applicationData) {
            $dealId = $applicationData['DEAL'];
            $contactId = $applicationData['CLIENT'];

            if (!$dealId) {
                return false;
            }

            if ($this->unbindAllContactsFromDeal($dealId) && $this->bindContactToDeal($dealId, $contactId)) {
                $result = $this->updateDealFields($dealId, [
                    'OPPORTUNITY' => $applicationData['AMOUNT_VALUE']
                ]);

                if ($result->isSuccess()) {
                    $this->logEvent(
                        Loc::getMessage('OTUS_DEAL_UPDATE_SUCCESS', ['#DEAL_ID#' => $dealId]),
                        \CEventLog::SEVERITY_INFO
                    );
                    return true;
                } else {
                    $this->logEvent(
                        Loc::getMessage('OTUS_DEAL_UPDATE_ERROR', ['#ERROR_MESSAGES#' => implode(', ', $result->getErrorMessages())]),
                        \CEventLog::SEVERITY_ERROR
                    );
                    return false;
                }
            }
        }

        $this->logEvent(Loc::getMessage('OTUS_APPLICATION_NOT_FOUND'), \CEventLog::SEVERITY_ERROR);
        return false;
    }

    /**
     * Получает данные заявки из инфоблока.
     *
     * @return array|null Массив данных заявки или null, если заявка не найдена
     */
    private function getApplicationData(): ?array
    {
        $data = ElementApplicationsTable::getList([
            'select' => [
                'AMOUNT_VALUE' => 'AMOUNT.IBLOCK_GENERIC_VALUE',
                'CLIENT' => 'CLIENT_ID.VALUE',
                'DEAL' => 'DEAL_ID.VALUE',
            ],
            'filter' => ['ID' => $this->applicationId]
        ])->fetch();

        return $data !== false ? $data : null;
    }

    /**
     * Обновляет данные сделки в CRM.
     *
     * @param int $dealId ID сделки
     * @param array $fields Массив полей для обновления
     * @return \Bitrix\Main\Result Результат выполнения операции
     */
    private function updateDealFields(int $dealId, array $fields): \Bitrix\Main\Result
    {
        return DealTable::update($dealId, $fields);
    }

    /**
     * Отвязывает все контакты от сделки.
     *
     * @param int $dealId ID сделки
     * @return bool Успех операции
     */
    private function unbindAllContactsFromDeal(int $dealId): bool
    {
        try {
            $contacts = DealContactTable::getList([
                'filter' => ['DEAL_ID' => $dealId],
                'select' => ['CONTACT_ID']
            ])->fetchAll();

            foreach ($contacts as $contact) {
                DealContactTable::unbindContactIDs($dealId, [$contact['CONTACT_ID']]);
            }
            $messageLog = Loc::getMessage('OTUS_CONTACT_UNBIND_SUCCESS', ['#DEAL_ID#' => $dealId]);
            $this->logEvent($messageLog, \CEventLog::SEVERITY_INFO);
            return true;
        } catch (\Exception $e) {
            $this->logEvent(
                Loc::getMessage('OTUS_CONTACT_UNBIND_ERROR', ['#ERROR_MESSAGE#' => $e->getMessage()]),
                \CEventLog::SEVERITY_ERROR
            );
            return false;
        }
    }

    /**
     * Привязывает контакт к сделке.
     *
     * @param int $dealId ID сделки
     * @param int $contactId ID контакта
     * @return bool Успех операции
     */
    private function bindContactToDeal(int $dealId, int $contactId): bool
    {
        try {
            DealContactTable::bindContactIDs($dealId, [$contactId]);
            $messageLog = Loc::getMessage('OTUS_CONTACT_BIND_SUCCESS', [
                '#CONTACT_ID#' => $contactId,
                '#DEAL_ID#' => $dealId
            ]);
            $this->logEvent($messageLog, \CEventLog::SEVERITY_INFO);
            return true;
        } catch (\Exception $e) {
            $this->logEvent(
                Loc::getMessage('OTUS_CONTACT_BIND_ERROR', ['#ERROR_MESSAGE#' => $e->getMessage()]),
                \CEventLog::SEVERITY_ERROR
            );
            return false;
        }
    }

    /**
     * Логирует событие в журнал Битрикса.
     *
     * @param string $message Сообщение для логирования
     * @param string $severity Уровень важности (INFO, WARNING, ERROR)
     * @return void
     */
    private function logEvent(string $message, string $severity): void
    {
        \CEventLog::Add([
            "SEVERITY" => $severity,
            "AUDIT_TYPE_ID" => "DEAL_UPDATE",
            "MODULE_ID" => "crm",
            "ITEM_ID" => $this->applicationId,
            "DESCRIPTION" => $message
        ]);
    }

    /**
     * Удаляет сделку на основе данных заявки.
     *
     * Метод получает данные заявки, извлекает идентификатор сделки и проверяет данные инфоблока.
     * Если данные корректны, создается экземпляр класса DealApplicationUpdater для удаления сделки.
     * В журнал логируется информация об удалении или ошибки.
     *
     * @return void
     */
    public function deleteDeal(): void
    {
        $applicationData = $this->getApplicationData();
        $dealId = $applicationData['DEAL'] ?? null;

        if ($dealId !== null) {
            $arApplication = $this->getIblockData($this->applicationId);

            if ($arApplication && $arApplication['IBLOCK_ID'] == \Otus\Iblock\IblockUtils::getIblockIdByCode('applications')) {

                $dealUpdater = new DealApplicationUpdater($dealId);
                $dealUpdater->delete();

                $this->logEvent(
                    Loc::getMessage('OTUS_DEAL_DELETE_SUCCESS', ['#DEAL_ID#' => $dealId]),
                    \CEventLog::SEVERITY_INFO
                );
            } else {
                $this->logEvent(
                    Loc::getMessage('OTUS_DEAL_DELETE_WARNING'),
                    \CEventLog::SEVERITY_WARNING
                );
            }
        } else {
            $this->logEvent(
                Loc::getMessage('OTUS_DEAL_DELETE_ERROR'),
                \CEventLog::SEVERITY_ERROR
            );
        }
    }

    /**
     * Получает данные инфоблока по ID элемента.
     *
     * @param int $elementId ID элемента
     * @return array|null Данные инфоблока или null, если элемент не найден
     */
    private function getIblockData(int $elementId): ?array
    {
        return \Bitrix\Iblock\ElementTable::getList([
            'select' => ['ID', 'NAME', 'IBLOCK_ID'],
            'filter' => ['ID' => $elementId]
        ])->fetch() ?: null;
    }
}
