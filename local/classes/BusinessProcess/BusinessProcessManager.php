<?php

namespace Otus\BusinessProcess;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Socialnetwork\LogTable;
use Bitrix\Main\Type\DateTime;

Loc::loadMessages(__FILE__);

class BusinessProcessManager
{
    public int $iblockId;
    public int $templateId;
    public array $parameters;
    public int $userId;
    public ?string $wfId;

    /**
     * Конструктор класса.
     *
     * @param int $iblockId ID информационного блока.
     * @param int $templateId ID шаблона бизнес-процесса.
     * @param array $parameters Параметры запуска бизнес-процесса.
     */
    public function __construct(int $iblockId, int $templateId, array $parameters = [])
    {
        $this->iblockId = $iblockId;
        $this->templateId = $templateId;
        $this->parameters = $parameters;
        $this->userId = !CurrentUser::get()->getId() ? 1 : CurrentUser::get()->getId();
        $this->wfId = null;
    }

    /**
     * Запускает бизнес-процесс.
     *
     * @return int|null Возвращает ID бизнес-процесса при успехе, иначе null.
     */
    public function startWorkflow(): ?int
    {
        // Подключение необходимых модулей
        if (!Loader::includeModule('socialnetwork')) {
            die(Loc::getMessage('OTUS_BP_SOCIALNETWORK_MODULE_NOT_INSTALLED'));
        }

        if (!Loader::includeModule('bizproc')) {
            die(Loc::getMessage('OTUS_BP_BIZPROC_MODULE_NOT_INSTALLED'));
        }

        $documentId = \CBPVirtualDocument::CreateDocument(
            0,
            [
                "IBLOCK_ID" => $this->iblockId,
                "NAME" => Loc::getMessage('OTUS_BP_REQUEST_FOR_PURCHASE'),
                "CREATED_BY" => "user_" . $this->userId,
            ],
        );

        $arErrorsTmp = [];
        $wfId = \CBPDocument::StartWorkflow(
            $this->templateId,
            ["lists", "BizprocDocument", $documentId],
            $this->parameters,
            $arErrorsTmp,
        );

        if ($wfId) {
            $this->wfId = $wfId;
        }
        return null;
    }

    /**
     * Логирует запуск бизнес-процесса в ленту новостей для указанных пользователей.
     *
     * @param array $userIds Массив ID пользователей.
     */
    public function logWorkflowStart(array $userIds): void
    {
        if ($this->wfId) {
            foreach ($userIds as $userId) {
                $logFields = [
                    'ENTITY_TYPE' => 'USER',
                    'ENTITY_ID' => $userId,
                    'EVENT_ID' => 'custom_workflow_notification',
                    'TITLE_TEMPLATE' => Loc::getMessage('OTUS_BP_WORKFLOW_STARTED_TITLE'),
                    'MESSAGE' => Loc::getMessage('OTUS_BP_WORKFLOW_STARTED_MESSAGE', ['#WF_ID#' => $this->wfId]),
                    'MODULE_ID' => 'bizproc',
                    'LOG_DATE' => new DateTime(),
                    'LOG_UPDATE' => new DateTime(),
                    'SOURCE_ID' => $this->wfId,
                ];

                LogTable::add($logFields);
            }
        }
    }
}
