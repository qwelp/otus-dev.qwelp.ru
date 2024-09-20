<?php

namespace Otus\Crm;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Loader;

Loader::includeModule('crm');

/**
 * Менеджер для работы со вкладками сущностей CRM
 */
class CrmTabs {
    public static function setCustomTabs(Event $event): EventResult
    {
        $tabs = $event->getParameter('tabs');
        // ID текущего элемента СРМ
        $entityID = $event->getParameter('entityID');
        // ID типа сущности: Сделка, Компания, Контакт и т.д.
        $entityTypeID = $event->getParameter('entityTypeID');

        // Проверяем, что открыта карточка именно Сделки
        if($entityTypeID == \CCrmOwnerType::Contact) {
            /*$tabs[] = [
                'id' => 'tab_garage',
                'name' => 'Гараж new',
                'loader' => [
                    'serviceUrl' => '/local/includes/crm/contact/tabs/tab_garage.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'CLIENT_ID' => $entityID
                        ]
                    ]
                ]
            ];*/
        }

        // Проверяем, что открыта карточка именно Сделки
        if($entityTypeID == \CCrmOwnerType::Deal) {
            $tabs[] = [
                'id' => 'custom',
                'name' => 'Автомобили',
                'loader' => [
                    'serviceUrl' => '/local/components/otusdev/tabs.views/templates/load_tab.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                    'componentData' => [
                        'template' => '',
                        'params' => []
                    ]
                ]
            ];
        }

        // Возвращаем модифицированный массив вкладок
        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
}
