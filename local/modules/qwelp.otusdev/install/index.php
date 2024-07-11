<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Qwelp\Otusdev\Models\Lists\CarsTable;

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
        $this->InstallEvents();
        $this->InstallFiles();
        ModuleManager::registerModule($this->MODULE_ID);

        $this->installDB();
    }

    public function doUninstall()
    {
        $this->uninstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function installDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            CarsTable::createTable();
            CarsTable::insertDemoData();
        }
    }

    public function uninstallDB()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            CarsTable::dropTable();
        }
    }

    public function InstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\Qwelp\Otusdev\Tabs',
            'setCustomTabs'
        );
    }

    public function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\Qwelp\Otusdev\Tabs',
            'setCustomTabs'
        );
    }

    function InstallFiles()
    {
        CopyDirFiles(
            __DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/local/components/otusdev",
            true,
            true
        );
        return true;
    }
}
