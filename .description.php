<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$description = array(
    'RETURN' => Loc::getMessage('SALE_TINKOFF_DESCRIPTION'),
);

if (IsModuleInstalled('crm'))
{
    $returnUrl = 'https://'.$_SERVER['HTTP_HOST'].'/bitrix/tools/sale_ps_success.php';
}
else
{
    $returnUrl = 'https://'.$_SERVER['HTTP_HOST'].'/personal/payment/success.php';
}

$data = array(
    'NAME'  => Loc::getMessage('SALE_TINKOFF_TITLE'),
    'SORT'  => 1000,
    'CODES' => array(
        "PAYMENT_METHOD" => array(
            "NAME"  => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_NAME"),
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    "full_prepayment"       => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PREPAYMENT"),
                    "prepayment"            => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_PREPAYMENT"),
                    "advance"               => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_ADVANCE"),
                    "full_payment"          => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_FULL_PAYMENT"),
                    "partial_payment "      => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_PAYMENT"),
                    "credit"                => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT"),
                    "credit_payment "       => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_CREDIT_PAYMENT")
                )
            ),
            "SORT"  => 100,
            'GROUP' => "GENERAL_SETTINGS",
        ),
        "PAYMENT_OBJECT" => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_PAYMENT_OBJECT_NAME"),
            "SORT"    => 110,
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    "commodity"             => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_COMMODITY"),
                    "excise"                => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_EXCISE"),
                    "job"                   => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_JOB"),
                    "service"               => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_SERVICE"),
                    "gambling_bet "         => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_BET"),
                    "gambling_prize"        => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_PRIZE"),
                    "lottery"               => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY"),
                    "lottery_prize"         => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_LOTTERY_PRIZE"),
                    "intellectual_activity" => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY"),
                    "payment"               => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_PAYMENT"),
                    "agent_commission"      => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_AGENT_COMMISSION"),
                    "composite"             => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_COMPOSITE"),
                    "another"               => Loc::getMessage("SALE_TINKOFF_PAYMENT_METHOD_ANOTHER")
                )
            ),
            'GROUP'   => "GENERAL_SETTINGS",
        ),
        "TERMINAL_ID" => array(
            "NAME"  => Loc::getMessage("SALE_TINKOFF_TERMINAL_ID_NAME"),
            "SORT"  => 120,
            "INPUT"   => array(
                "TYPE" => "STRING"
            ),
            'GROUP' => "GENERAL_SETTINGS",
        ),
        "SHOP_SECRET_WORD"  => array(
            "NAME"  => Loc::getMessage("SALE_TINKOFF_SHOP_SECRET_WORD_NAME"),
            "SORT"  => 130,
            "INPUT"   => array(
                "TYPE" => "STRING"
            ),
            'GROUP' => "GENERAL_SETTINGS",
        ),
        "ENABLE_TAXATION" => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_ENABLE_TAXATION_NAME"),
            "SORT"    => 140,
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    '0' => Loc::getMessage('SALE_TINKOFF_NO'),
                    '1' => Loc::getMessage('SALE_TINKOFF_YES')
                )
            ),
            'GROUP'   => "GENERAL_SETTINGS",
        ),
        "DELIVERY_TAXATION"  => array(
            "NAME"  => Loc::getMessage("SALE_TINKOFF_DELIVERY_TAXATION_NAME"),
            "SORT"  => 150,
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    'none' => Loc::getMessage('SALE_TINKOFF_VAT_NONE'),
                    'vat0' => Loc::getMessage('SALE_TINKOFF_VAT_ZERO'),
                    'vat10' => Loc::getMessage('SALE_TINKOFF_VAT_REDUCED'),
                    'vat18' => Loc::getMessage('SALE_TINKOFF_VAT_STANDARD'),
                    'vat20' => Loc::getMessage('SALE_TINKOFF_VAT_TWENTY')
                )
            ),
            'GROUP' => "GENERAL_SETTINGS",
        ),
        "TAXATION"  => array(
            "NAME"  => Loc::getMessage("SALE_TINKOFF_TAXATION_NAME"),
            "SORT"  => 160,
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    'osn' => Loc::getMessage('SALE_TINKOFF_TAXATION_OSN'),
                    'usn_income' => Loc::getMessage('SALE_TINKOFF_TAXATION_USN_IMCOME'),
                    'usn_income_outcome' => Loc::getMessage('SALE_TINKOFF_TAXATION_USN_IMCOME_OUTCOME'),
                    'envd' => Loc::getMessage('SALE_TINKOFF_TAXATION_ENVD'),
                    'esn' => Loc::getMessage('SALE_TINKOFF_TAXATION_ESN'),
                    'patent' => Loc::getMessage('SALE_TINKOFF_TAXATION_PATENT')
                )
            ),
            'GROUP' => "GENERAL_SETTINGS",
        ),
        "EMAIL_COMPANY" => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_EMAIL_COMPANY_NAME"),
            "SORT"    => 170,
            "INPUT"   => array(
                "TYPE" => "STRING"
            ),
            'GROUP'   => "GENERAL_SETTINGS",
        ),
        "LANGUAGE_PAYMENT"    => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_LANGUAGE_NAME"),
            "SORT"    => 180,
            "INPUT" => array(
                'TYPE'    => 'ENUM',
                'OPTIONS' => array(
                    "en" => Loc::getMessage("SALE_TINKOFF_LANGUAGE_EN"),
                    "ru" => Loc::getMessage("SALE_TINKOFF_LANGUAGE_RU")
                )
            ),
            'GROUP'   => "GENERAL_SETTINGS",
        ),
        'CLIENT_PHONE' => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_CLIENT_PHONE_NAME"),
            "SORT"    => 190,
            'DEFAULT' => array(
                'PROVIDER_KEY'   => 'CRM_CONTACT',
                'PROVIDER_VALUE' => 'PHONE_WORK'
            )
        ),
        'CLIENT_EMAIL' => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_CLIENT_EMAIL_NAME"),
            "SORT"    => 190,
            'DEFAULT' => array(
                'PROVIDER_KEY'   => 'CRM_CONTACT',
                'PROVIDER_VALUE' => 'EMAIL_WORK'
            )
        ),
        'CLIENT_NAME' => array(
            "NAME"    => Loc::getMessage("SALE_TINKOFF_CLIENT_NAME_NAME"),
            "SORT"    => 190,
            'DEFAULT' => array(
                'PROVIDER_KEY'   => 'CRM_CONTACT',
                'PROVIDER_VALUE' => 'FULL_NAME'
            )
        )
    )
);
