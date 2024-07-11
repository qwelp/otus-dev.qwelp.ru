<?php

namespace Qwelp\Otusdev;

use Bitrix\Main\EventResult;
use Bitrix\Main\Event;

class Tabs
{
    public static function setCustomTabs(Event $event): EventResult
    {
        $entityTypeID = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');

        if ($entityTypeID == \CCrmOwnerType::Deal) {
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

        return new EventResult(EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }
}
