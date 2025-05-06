<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Admin menu for installment payment module
 */
$aMenu = array(
    'parent_menu' => 'global_menu_store',
    'section' => 'installment_payment',
    'sort' => 500,
    'text' => Loc::getMessage('INSTALLMENT_PAYMENT_MENU_TITLE'),
    'title' => Loc::getMessage('INSTALLMENT_PAYMENT_MENU_TITLE'),
    'icon' => 'installment_payment_menu_icon',
    'page_icon' => 'installment_payment_page_icon',
    'items_id' => 'menu_installment_payment',
    'items' => array(
        array(
            'text' => Loc::getMessage('INSTALLMENT_PAYMENT_MENU_SETTINGS'),
            'url' => 'installment_payment_settings.php?lang=' . LANGUAGE_ID,
            'more_url' => array('installment_payment_settings.php'),
            'title' => Loc::getMessage('INSTALLMENT_PAYMENT_MENU_SETTINGS'),
        ),
    )
);

return $aMenu;
