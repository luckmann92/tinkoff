<?
namespace Sale\Handlers\PaySystem;

include(dirname(__FILE__) . "/sdk/tinkoff_autoload.php");

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Request,
    Bitrix\Sale\Payment,
    Bitrix\Sale\PaySystem,
    TinkoffMerchantAPI;

Loc::loadMessages(__FILE__);


class TinkoffHandler extends PaySystem\ServiceHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     * @throws \HttpException
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {

        $shouldPay = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"];
        $invoiceId = $this->getBusinessValue($payment, 'ORDER_ID');
        $clientName = $this->getBusinessValue($payment, 'CLIENT_NAME');
        $clientEmail = $this->getBusinessValue($payment, 'CLIENT_EMAIL');
        $clientPhone = $this->getBusinessValue($payment, 'CLIENT_PHONE');
        $isTaxationEnabled = $this->getBusinessValue($payment, 'ENABLE_TAXATION');

        $params = array(
            'OrderId' => $this->getBusinessValue($payment, 'ORDER_ID'),
            'Amount' => round($shouldPay * 100),
            'Language' => $this->getBusinessValue($payment, 'LANGUAGE_PAYMENT'),
            'DATA' => array(
                'Email' => $clientEmail,
                'Name' => $clientName,
                'Connection_type' => 'Bitrix_2.0.1_atol'
            ),
        );

        if ($clientPhone) {
            $params['DATA']['Phone'] = $clientPhone;
        }

        $invoice = \CCrmInvoice::GetByID($invoiceId, false);

        if ($isTaxationEnabled) {
            $items = array();
            $productsList = \CCrmInvoice::GetProductRows($invoiceId);

            foreach ($productsList as $product) {
                $tax = (int)round($product['VAT_RATE']);

                if ($tax === 20) {
                    $vat = 'vat20';
                } elseif ($tax === 18) {
                    $vat = 'vat18';
                } elseif ($tax === 10) {
                    $vat = 'vat10';
                } elseif ($tax === 0) {
                    $vat = 'vat0';
                } else {
                    $vat = 'none';
                }

                $items[] = array(
                    "Name" => mb_substr($this->convertEncodingTinkoff($product['NAME']), 0, 64),
                    "Price" => round($product['PRICE'] * 100),
                    "Quantity" => round($product['QUANTITY'], 3, PHP_ROUND_HALF_UP),
                    "Amount" => round($product['PRICE'] * $product['QUANTITY'] * 100),
                    "PaymentMethod" => trim($this->getBusinessValue($payment, 'PAYMENT_METHOD')),
                    "PaymentObject" => trim($this->getBusinessValue($payment, 'PAYMENT_OBJECT')),
                    "Tax" => $vat,
                );
            }
        }

        if (is_numeric($invoice['UF_CRM_5CBD6181DF560'])) {
            $org = \CIBlockElement::GetByID($invoice['UF_CRM_5CBD6181DF560'])->Fetch()['NAME'];
        } else {
            $org = Loc::getMessage('SALE_TINKOFF_PAYMENT_NOT_SPECIFIED');
        }

        $extraParams = array(
            'TERMINAL_ID'      => $this->getBusinessValue($payment, 'TERMINAL_ID'),
            'SHOP_SECRET_WORD' => $this->getBusinessValue($payment, 'SHOP_SECRET_WORD'),
            'DESCRIPTION'      => Loc::getMessage("PAYMENT_PAYMO_DESCRIPTION").$this->getBusinessValue($payment, 'ORDER_ID'),
            'EXTRA_INVOICE_ID'   => $invoiceId,
            'EXTRA_ADDRESS'     => $invoice['UF_CRM_5C2368D72C1C6'],
            'EXTRA_RESONAL_ACCOUNT'  => $invoice['UF_CRM_5C2368D78E0C8'],
            'EXTRA_DEAL_ID'     => $invoice['UF_DEAL_ID'],
            'EXTRA_ORGANIZATION'     => $org,
        );

        $bankHandler = new TinkoffMerchantAPI($extraParams["TERMINAL_ID"], $extraParams["TERMINAL_ID"]);
        $request = $bankHandler->buildQuery('Init', $params);
        $this->logsTinkoff($params, $request);

        $request = json_decode($request);
        $extraParams['PAYMENT_URL'] = $request->PaymentURL;

        $this->setExtraParams($extraParams);






        return $this->showTemplate($payment, 'template');

    }

    /**
     * @param \Bitrix\Sale\Payment $payment
     * @param \Bitrix\Main\Request $request
     * @return PaySystem\ServiceResult|void
     */
    function processRequest(\Bitrix\Sale\Payment $payment, \Bitrix\Main\Request $request)
    {

    }

    /**
     * @param \Bitrix\Main\Request $request
     * @return mixed|void
     */
    function getPaymentIdFromRequest(\Bitrix\Main\Request $request)
    {
        // TODO: Implement getPaymentIdFromRequest() method.
    }

    /**
     * @return array|void
     */
    function getCurrencyList()
    {
        // TODO: Implement getCurrencyList() method.
    }

    /**
     * @param $orderID
     * @return bool
     */
    function getOwnerEmailTinkoff($orderID)
    {
        //ищем в свойствах заказа e-mail
        $res = CSaleOrderPropsValue::GetOrderProps($orderID);

        while ($row = $res->fetch()) {
            if ($row['IS_EMAIL'] == 'Y' && check_email($row['VALUE'])) {
                return $row['VALUE'];
            }
        }
        //если не нашли, берем mail пользователя при регистрации
        if ($order = CSaleOrder::getById($orderID)) {
            if ($user = CUser::GetByID($order['USER_ID'])->fetch()) {
                return $user['EMAIL'];
            }
        }

        return false;
    }

    /**
     * @param $orderID
     * @param $user
     * @return bool
     */
    function getOwnerPhoneTinkoff($orderID, $user)
    {
        //ищем в свойствах заказа PHONE
        $res = CSaleOrderPropsValue::GetOrderProps($orderID);

        while ($row = $res->fetch()) {
            if ($row['CODE'] == 'PHONE') {
                if (!empty($phone = $row['VALUE'])) {
                    return $phone;
                }
            }
        }
        //если не нашли, берем PHONE пользователя при регистрации
        if (!empty($phone = $user->arResult[0]['PERSONAL_PHONE'])) {
            return $phone;
        }

        return false;
    }

    /**
     * @param $orderID
     * @return bool|string
     */
    function getOwnerFioTinkoff($orderID)
    {
        //ищем в свойствах заказа fio
        $res = CSaleOrderPropsValue::GetOrderProps($orderID);

        while ($row = $res->fetch()) {
            if ($row['IS_PROFILE_NAME'] == 'Y') {
                return $row['VALUE'];
            }
        }
        //если не нашли, берем fio пользователя при регистрации
        if ($order = CSaleOrder::getById($orderID)) {
            if ($user = CUser::GetByID($order['USER_ID'])->fetch()) {
                $fio = implode(" ", array($user['LAST_NAME'], $user['NAME'], $user['SECOND_NAME']));
                return $fio;
            }
        }

        return false;
    }

    /**
     * @param $data
     * @return string
     */
    function convertEncodingTinkoff($data)
    {
        return mb_convert_encoding($data, "UTF-8", LANG_CHARSET);
    }

    /**
     * @param $paymentData
     * @param $request
     */
    function logsTinkoff($paymentData, $request)
    {
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= json_encode($paymentData, JSON_UNESCAPED_UNICODE);
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/tinkoff.log", $log, FILE_APPEND);

        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= $request;
        $log .= "\n";
        file_put_contents(dirname(__FILE__) . "/tinkoff.log", $log, FILE_APPEND);
    }
}
