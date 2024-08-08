<?php

namespace Otus\RestService;

use Bitrix\Main\Localization\Loc;
use Bitrix\Rest\RestException;
use Otus\Orm\CrudTable;

class RestServiceCRUD
{
    public static function OnRestServiceBuildDescriptionHandler()
    {
        Loc::getMessage('REST_SCOPE_OTUS.TESTCRUD');

        return [
            'otus.testcrud' => [
                'otus.testcrud.add' => [__CLASS__, 'add'],
                'otus.testcrud.list' => [__CLASS__, 'getList'],
                'otus.testcrud.update' => [__CLASS__, 'update'],
                'otus.testcrud.delete' => [__CLASS__, 'delete'],
                \CRestUtil::EVENTS => [
                    'onAfterCrudTableDAdd' => [
                        'main',
                        'onAfterCrudTable',
                        [__CLASS__, 'prepareEventData']
                    ]
                ]
            ]
        ];
    }

    public static function add($arParams, $navStart, \CRestServer $server)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logRest.txt', 'add: ' . var_export($arParams, true) . PHP_EOL, FILE_APPEND);

        $originDataStoreResult = CrudTable::add($arParams);
        if ($originDataStoreResult->isSuccess()) {
            return $originDataStoreResult->getId();
        } else {
            throw new RestException(
                json_encode($originDataStoreResult->getErrorMessages(), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT, \CRestServer::STATUS_OK
            );
        }
    }

    public static function getList($arParams, $navStart, \CRestServer $server)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logRest.txt', 'LIST_PARAMS: ' . var_export($arParams, true) . PHP_EOL, FILE_APPEND);

        $arElements = CrudTable::getList([
            'filter' => $arParams['filter'] ?: [],
            'select' => $arParams['select'] ?: ['*'],
            'order' => $arParams['order'] ? [$arParams['order']['by'] => $arParams['order']['direction']] : ['ID' => 'ASC'],
            'group' => $arParams['group'] ?: [],
            'limit' => $arParams['limit'] ?: 0,
            'offset' => $arParams['offset'] ?: 0,
        ])->fetchAll();

        return $arElements;
    }

    public static function update($arParams, $navStart, \CRestServer $server)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logRest.txt', 'update: ' . var_export($arParams, true) . PHP_EOL, FILE_APPEND);

        $entityId = intval($arParams['ID']);
        unset($arParams['ID']);

        $originDataStoreResult = CrudTable::update($entityId, $arParams);
        if ($originDataStoreResult->isSuccess()) {
            return $originDataStoreResult->getId();
        } else {
            throw new RestException(
                json_encode($originDataStoreResult->getErrorMessages(), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT, \CRestServer::STATUS_OK
            );
        }
    }

    public static function delete($arParams, $navStart, \CRestServer $server)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/logRest.txt', 'delete: ' . var_export($arParams, true) . PHP_EOL, FILE_APPEND);

        $entityId = intval($arParams['ID']);
        $originDataStoreResult = CrudTable::delete($entityId);

        if ($originDataStoreResult->isSuccess()) {
            return true;
        } else {
            throw new RestException(
                json_encode($originDataStoreResult->getErrorMessages(), JSON_UNESCAPED_UNICODE),
                RestException::ERROR_ARGUMENT, \CRestServer::STATUS_OK
            );
        }
    }

    public static function prepareEventData($arguments, $handler)
    {
        $event = reset($arguments);
        $response = $event->getParameters();
        return $response;
    }
}
