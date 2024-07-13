<?php
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('DisableEventsCheck', true);
@define('PUBLIC_AJAX_MODE', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;

Loader::includeModule('timeman');

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$userId = CurrentUser::get()->getId();

$tmUser = new CTimeManUser($userId);

switch ($request['type']) {
    case 'start':
        $tmUser->OpenDay();
        $tmUser->ReopenDay();
        break;
    case 'end':
        $tmUser->CloseDay();
        break;
}

echo \Bitrix\Main\Web\Json::encode([
    'status' => $tmUser->State(),
]);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
