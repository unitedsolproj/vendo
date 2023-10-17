<?php
/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class VendopaymentValidatePixModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        
        if ($action =='pix') {
            $pay_status = $this->makePixPayment();
            $error_code=1;
            $this->context->smarty->assign('error_code', $error_code);
            $this->context->smarty->assign('pay_status', $pay_status);
            $this->setTemplate(
                'module:' . $this->module->name . '/views/templates/front/payment_process.tpl'
            );
        }
    }

    public function makePixPayment()
    {
        $id_customer = $this->context->customer->id;
        $customer = new Customer($id_customer);
        $customer_fname = $customer->firstname;
        $customer_lname = $customer->lastname;
        $customer_email = $customer->email;
        $account_title = Configuration::get('VENDOPAYMENT_ACCOUNT_TITLE');
        $merchant_id = Configuration::get('VENDOPAYMENT_ACCOUNT_MERCHANT_ID');
        $site_id = Configuration::get('VENDOPAYMENT_ACCOUNT_SITE_ID');
        $success_url = '';
        $back_url = '';
        $account_desc = Configuration::get('VENDOPAYMENT_ACCOUNT_DESCRIPTION');
        $secret_key = Configuration::get('VENDOPAYMENT_ACCOUNT_SECRET_LIVE');
        $secret_key_test = Configuration::get('VENDOPAYMENT_ACCOUNT_SECRET_TEST');
        $id_cart = $this->context->cart->id;
        $cart = new Cart($id_cart);
        $total_to_pay = (float)($cart->getOrderTotal(true, Cart::BOTH));
        $cart_products = $cart->getProducts();
        $id_language = $this->context->language->id;
        $language = new Language($id_language);
        $language_code = $language->iso_code;
        $id_address_delivery = $cart->id_address_delivery;
        $customer_address = new Address($cart->id_address_delivery);
        $customer_city = $customer_address->city;
        $customer_country = $customer_address->country;
        $customer_postcode = $customer_address->postcode;
        $customer_phone = $customer_address->phone;
        $address1 = $customer_address->address1;
        $id_country = $customer_address->id_country;
        $country_code = Country::getIsoById($id_country);
        $customer_ip = $_SERVER['REMOTE_ADDR'];
        $action_url = $this->context->link->getModuleLink("vendopayment", "validatepay");
        include(dirname(_PS_MODULE_DIR_).'/modules/vendopayment/vendor/autoload.php');

        try {
            $payment = new \VendoSdk\S2S\Request\Payment();
            $payment->setApiSecret($secret_key);
            $payment->setMerchantId($merchant_id);
            $payment->setSiteId($site_id);
            $payment->setAmount($total_to_pay);
            $payment->setCurrency(\VendoSdk\Vendo::CURRENCY_USD);
            $payment->setIsTest(true);
            $payment->setIsMerchantInitiatedTransaction(false);
            $externalRef = new \VendoSdk\S2S\Request\Details\ExternalReferences();
            $externalRef->setTransactionReference('Prestashop#');
            $payment->setExternalReferences($externalRef);

            /**
             * Customer details
             */
            $customer = new \VendoSdk\S2S\Request\Details\Customer();
            $customer->setFirstName($customer_fname);
            $customer->setLastName($customer_lname);
            $customer->setEmail($customer_email);

            /**
             * Payment details
             */
            $paymentDetails = new \VendoSdk\S2S\Request\Details\PaymentMethod\Pix();
            $payment->setPaymentDetails($paymentDetails);

            $payment->setPaymentDetails($paymentDetails);
            $customer->setLanguageCode($language_code);
            $customer->setCountryCode('BR');
            $cpf = '723.785.048-29';
            $cpf = Tools::getValue('cpf');
            $customer->setNationalIdentifier($cpf);
            $payment->setCustomerDetails($customer);

            /**
             * Shipping details. This is required.
             */
            $shippingAddress = new \VendoSdk\S2S\Request\Details\ShippingAddress();
            $shippingAddress->setFirstName($customer_fname);
            $shippingAddress->setLastName($customer_lname);
            $shippingAddress->setAddress($address1);
            $shippingAddress->setCountryCode($country_code);
            $shippingAddress->setCity($customer_city);
            $shippingAddress->setState($customer_country);
            $shippingAddress->setPostalCode($customer_postcode);
            $shippingAddress->setPhone('10');
            $payment->setShippingAddress($shippingAddress);

            /**
             * User request details
             */
            $request = new \VendoSdk\S2S\Request\Details\ClientRequest();
            $request->setIpAddress($_SERVER['REMOTE_ADDR'] ?: '127.0.0.1');//you must pass a valid IPv4 address
            $request->setBrowserUserAgent($_SERVER['HTTP_USER_AGENT'] ?: null);
            $payment->setRequestDetails($request);
            $response = $payment->postRequest();

            if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
                $transaction_id = $response->getTransactionDetails()->getId();
                $transaction_reference = $response->getExternalReferences()->getTransactionReference();
                $this->placeOrder($transaction_id);
                return 1;
            } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
                //fail
                return 2;
            } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_VERIFICATION_REQUIRED) {
                $ttoken = $response->getPaymentToken();
                $redirect_url = $response->getResultDetails()->getVerificationUrl();

                $transaction_id = $response->getTransactionDetails()->getId();
                $id_cart = $this->context->cart->id;
                $id_customer = $this->context->customer->id;
                $type = 'pix';
                Db::getInstance()->insert('vendopayment', array('token'=>pSQL($ttoken),
                'transaction_id'=>pSQL($transaction_id),
                'type'=>pSQL($type),
                'id_cart'=>pSQL($id_cart), 'id_customer'=>pSQL($id_customer)));
                sleep(7);
                Tools::redirect($redirect_url);
                return 3;
            }
        } catch (\VendoSdk\Exception $exception) {
            //fail
            return 4;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            //fail
            return 4;
        }
    }

    public function placeOrder($transaction_id, $status=null) {
        if ($status == 'pending'){
            $order_state = Configuration::get('VENDO_OS_PENDING');
        } else {
            $order_state = Configuration::get('VENDO_OS_SUCCESS');
        }
        $customer = new Customer($this->context->customer->id);
        $module='vendopayment';
        $cart_total = $this->context->cart->getOrderTotal(true, Cart::BOTH);
        $paymentModule = Module::getInstanceByName($module);
        $paymentModule->validateOrder(
            $this->context->cart->id,
            $order_state,
            (float) $this->context->cart->getOrderTotal(true, Cart::BOTH),
            $paymentModule->displayName,
            null,
            null,
            (int) $this->context->currency->id,
            false,
            $customer->secure_key
        );
        $order_id = $paymentModule->currentOrder;
        $order = new Order($order_id);
        $customer = new Customer($this->context->customer->id);
        $order_key = $order->secure_key;
        $language_id = (int)$this->context->language->id;
        $controller = 'order-confirmation';

        $this->setTransactionId($order_id, $transaction_id, $cart_total);
        $link = new Link();
        $orderLink = $link->getPageLink(
            'order-confirmation',
            null,
            (int) Context::getContext()->language->id,
            [
                'id_cart' => (int) $this->context->cart->id,
                'id_module' => (int) $this->module->id,
                'id_order' => (int) $this->module->currentOrder,
                'key' => $customer->secure_key,
            ]
        );
        Tools::redirect($orderLink);
    }

    public function setTransactionId($order_id, $transaction_id, $cart_total)
    {
        $order = new Order($order_id);
        $order_payment = new OrderPayment();
        $payment_method = 'Vendo Service';
        $order_payment->order_reference = $order->reference;
        $order_payment->id_currency = $order->id_currency;
        $order_payment->id_order = $order_id;
        $order_payment->transaction_id = $transaction_id;
        $order_payment->payment_method = $payment_method;
        $order_payment->amount = $cart_total;
        $order_payment->add();
    }

    public function getCartTransaction($id_cart)
    {
        $sql = new DbQuery();
        $sql->select('transaction_id');
        $sql->from('vendopayment');
        $sql->where('id_cart = '.(int)$id_cart);
        return Db::getInstance()->getValue($sql);
    }
}
