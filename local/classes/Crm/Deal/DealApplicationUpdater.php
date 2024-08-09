<?php

namespace Otus\Crm\Deal;

use Bitrix\Main\Loader;
use Bitrix\Crm\Binding\DealContactTable;
use Bitrix\Iblock\Elements\ElementApplicationsTable;
use Bitrix\Crm\DealTable;

/**
 * Класс для обновления данных заявки на основе данных сделки.
 */
class DealApplicationUpdater
{
    /**
     * @var int ID сделки
     */
    private $dealId;

    /**
     * Конструктор класса DealApplicationUpdater.
     *
     * @param int $dealId ID сделки
     */
    public function __construct(int $dealId)
    {
        $this->dealId = $dealId;
        $this->includeModules();
    }

    /**
     * Загружает необходимые модули.
     *
     * @return void
     * @throws \Exception
     */
    private function includeModules(): void
    {
        if (!Loader::includeModule('iblock') || !Loader::includeModule('crm')) {
            throw new \Exception('Не удалось загрузить модули iblock и/или crm.');
        }
    }

    /**
     * Обновляет данные заявки на основе данных сделки.
     *
     * @return void
     */
    public function updateDealApplication(): void
    {
        $dealData = $this->getDealData();
        $dealContacts = $this->getDealContacts();

        $applicationData = $this->getApplicationData();

        if ($applicationData) {
            $this->updateApplication($applicationData['ID'], $dealData['OPPORTUNITY'], $dealContacts);
            $this->logEvent("Заявка (ID: {$applicationData['ID']}) успешно обновлена на основе данных сделки (ID: {$this->dealId}).", \CEventLog::SEVERITY_INFO);
        } else {
            $this->logEvent("Не удалось найти заявку, связанную с сделкой (ID: {$this->dealId}).", \CEventLog::SEVERITY_ERROR);
        }
    }

    /**
     * Получает данные сделки.
     *
     * @return array|null Массив данных сделки или null, если данные не найдены
     */
    private function getDealData(): ?array
    {
        return DealTable::getList([
            'select' => ['OPPORTUNITY'],
            'filter' => ['ID' => $this->dealId],
        ])->fetch();
    }

    /**
     * Получает контакты, связанные с данной сделкой.
     *
     * @return array Массив ID контактов
     */
    private function getDealContacts(): array
    {
        $contacts = DealContactTable::getList([
            'filter' => ['DEAL_ID' => $this->dealId],
            'select' => ['CONTACT_ID']
        ])->fetchAll();

        return array_map(function ($contact) {
            return $contact['CONTACT_ID'];
        }, $contacts);
    }

    /**
     * Получает данные заявки, связанной с данной сделкой.
     *
     * @return array|null Массив данных заявки или null, если данные не найдены
     */
    private function getApplicationData(): ?array
    {
        $arApplications = ElementApplicationsTable::getList([
            'select' => [
                'ID',
                'DEAL_ID_VALUE' => 'DEAL_ID.VALUE'
            ],
            'filter' => ['DEAL_ID_VALUE' => $this->dealId],
        ])->fetch();

        if ($arApplications) {
            return $arApplications;
        }
        return null;
    }

    /**
     * Обновляет данные заявки.
     *
     * @param int $applicationId ID заявки
     * @param float $amount Сумма сделки
     * @param array $contactIds Массив ID контактов
     * @return void
     */
    private function updateApplication(int $applicationId, float $amount, array $contactIds): void
    {
        $propertyValues = [
            'AMOUNT' => $amount,
            'CLIENT_ID' => $contactIds
        ];
        \CIBlockElement::SetPropertyValuesEx($applicationId, IBLOCK_ID_OTUS_APPLICATIONS, $propertyValues);
    }

    /**
     * Логирует событие.
     *
     * @param string $message Сообщение для логирования
     * @param string $severity Уровень важности (INFO, WARNING, ERROR)
     * @return void
     */
    private function logEvent(string $message, string $severity): void
    {
        \CEventLog::Add([
            "SEVERITY" => $severity,
            "AUDIT_TYPE_ID" => "APPLICATIONS_UPDATE",
            "MODULE_ID" => "crm",
            "ITEM_ID" => $this->dealId,
            "DESCRIPTION" => $message
        ]);
    }

    /**
     * Удаляет заявку по сделке.
     *
     * Метод получает данные заявки по текущей сделке.
     * Если данные заявки найдены, метод удаляет заявку из базы данных.
     *
     * @return void
     */
    public function deleteApplication(): void
    {
        $arApplicationData = $this->getApplicationData();

        if ($arApplicationData && $arApplicationData['ID']) {
            ElementApplicationsTable::delete($arApplicationData['ID']);
        }
    }

    /**
     * Удаляет сделку по ее идентификатору.
     *
     * @param int $dealId Идентификатор сделки
     * @return void
     */
    public function delete(): void
    {
        DealTable::delete($this->dealId);
    }
}
