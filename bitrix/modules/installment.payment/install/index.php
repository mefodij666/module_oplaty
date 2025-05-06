<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class installment_payment extends CModule
{
    public $MODULE_ID = "installment.payment";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . "/version.php");
        
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        
        $this->MODULE_NAME = Loc::getMessage("INSTALLMENT_PAYMENT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("INSTALLMENT_PAYMENT_MODULE_DESC");
        
        $this->PARTNER_NAME = Loc::getMessage("INSTALLMENT_PAYMENT_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage("INSTALLMENT_PAYMENT_PARTNER_URI");
    }
    
    public function DoInstall()
    {
        global $APPLICATION;
        
        if (!$this->isVersionCompatible()) {
            $APPLICATION->ThrowException(
                Loc::getMessage("INSTALLMENT_PAYMENT_INSTALL_ERROR_VERSION")
            );
            return false;
        }
        
        $this->InstallFiles();
        $this->InstallDB();
        
        ModuleManager::registerModule($this->MODULE_ID);
        
        return true;
    }
    
    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        
        ModuleManager::unRegisterModule($this->MODULE_ID);
        
        return true;
    }
    
    /**
     * Проверка на совместимость с текущей версией Битрикс
     */
    public function isVersionCompatible()
    {
        return CheckVersion(ModuleManager::getVersion("main"), "14.00.00");
    }
    
    /**
     * Установка файлов модуля
     */
    public function InstallFiles()
    {
        // Копируем компоненты
        CopyDirFiles(
            __DIR__ . "/components",
            Application::getDocumentRoot() . "/bitrix/components",
            true,
            true
        );
        
        // Копируем CSS и JS файлы
        CopyDirFiles(
            __DIR__ . "/js",
            Application::getDocumentRoot() . "/bitrix/js",
            true,
            true
        );
        CopyDirFiles(
            __DIR__ . "/css",
            Application::getDocumentRoot() . "/bitrix/css",
            true,
            true
        );
        
        // Копируем файлы административного интерфейса
        CopyDirFiles(
            __DIR__ . "/admin",
            Application::getDocumentRoot() . "/bitrix/admin",
            true,
            true
        );
        
        return true;
    }
    
    /**
     * Удаление файлов модуля
     */
    public function UnInstallFiles()
    {
        // Удаляем компоненты
        Directory::deleteDirectory(
            Application::getDocumentRoot() . "/bitrix/components/installment"
        );
        
        // Удаляем CSS и JS файлы
        Directory::deleteDirectory(
            Application::getDocumentRoot() . "/bitrix/js/installment.payment"
        );
        Directory::deleteDirectory(
            Application::getDocumentRoot() . "/bitrix/css/installment.payment"
        );
        
        // Удаляем файлы административного интерфейса
        DeleteDirFiles(
            __DIR__ . "/admin",
            Application::getDocumentRoot() . "/bitrix/admin"
        );
        
        return true;
    }
    
    /**
     * Установка БД
     */
    public function InstallDB()
    {
        // Устанавливаем настройки модуля по умолчанию
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_2", "0");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_3", "0");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_4", "0");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_5", "0");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_6", "0");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_9", "3");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_12", "5");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_18", "7");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_24", "10");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_36", "15");
        Option::set($this->MODULE_ID, "MARKUP_PERCENT_48", "20");
        Option::set($this->MODULE_ID, "MIN_ORDER_AMOUNT", "100");
        Option::set($this->MODULE_ID, "DETAILS_LINK", "/installment-info/");
        
        return true;
    }
    
    /**
     * Удаление БД
     */
    public function UnInstallDB()
    {
        // Удаляем настройки модуля
        Option::delete($this->MODULE_ID);
        
        return true;
    }
}