<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class qwelp_otusdev extends CModule
{
    public function __construct()
    {
        $arModuleVersion = array();
        
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        
        $this->MODULE_ID = 'qwelp.otusdev';
        $this->MODULE_NAME = Loc::getMessage('QWELP_DEV_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('QWELP_DEV_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('QWELP_DEV_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = 'https://otus-dev.qwelp.ru/';
    }

    public function doInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function doUninstall()
    {

    }

    public function installDB()
    {

    }

    public function uninstallDB()
    {

    }
}
