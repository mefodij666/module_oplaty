<?php
/**
 * Файл содержит основные функции и классы модуля "Оплата частями"
 *
 * @package installment.payment
 */

use Bitrix\Main\Loader;

// Автозагрузка классов модуля
Loader::registerAutoLoadClasses(
    "installment.payment",
    array(
        "Installment\\Payment\\Calculator" => "lib/calculator.php",
        "Installment\\Payment\\Helper" => "lib/helper.php",
    )
);