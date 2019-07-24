<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($params['PAYMENT_URL']) {?>
    <form action="<?=$params['PAYMENT_URL']?>" METHOD="GET" target="_blank">
        <input type="submit" value="<?=Loc::getMessage("SALE_TINKOFF_PAYBUTTON_NAME")?>">
    </form>
<?} else {?>
    <b><?=Loc::getMessage("SALE_TINKOFF_UNAVAILABLE")?></b>
<?}?>
