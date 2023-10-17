<?php
/**
* 2007-2023 PrestaShop
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
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Vendopayment extends PaymentModule
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'vendopayment';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'FMM';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;
        $this->VPSettings = $this->getConfigFormValues();
        parent::__construct();

        $this->displayName = $this->l('Vendo Payment Service Module');
        $this->description = $this->l('Vendo Payment Service Module');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        Configuration::updateValue('VENDOPAYMENT_LIVE_MODE', false);
        Configuration::updateValue('VENDOPAYMENT_CARD_MODE', false);
        Configuration::updateValue('VENDOPAYMENT_PIX_MODE', false);
        Configuration::updateValue('VENDOPAYMENT_SEPA_MODE', false);
        Configuration::updateValue('VENDOPAYMENT_CRYPTO_MODE', false);
        $this->installOrderState();
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('ModuleRoutes') &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        Configuration::deleteByName('VENDOPAYMENT_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitVendopaymentModule')) == true) {
            $response = $this->postProcess();
            if ($response == 1) {
                $this->context->controller->errors[]= $this->l('Required Field Missing');
            } else {
                $this->context->controller->confirmations[]= $this->l('Successfully Updated');
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);
        $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        if ($force_ssl) {
            $base_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        } else {
            $base_url = _PS_BASE_URL_.__PS_BASE_URI__;
        }
        $successurl = $base_url.'vendo_payment?action=success&reference={REF}&email={EMAIL}';
        $this->context->smarty->assign('successurl',$successurl);

        $postbackurl = $base_url.'vendo_postback';
        $this->context->smarty->assign('postbackurl',$postbackurl);
        
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitVendopaymentModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'VENDOPAYMENT_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable Card Payment'),
                        'name' => 'VENDOPAYMENT_CARD_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable if you want to show Card payment on front'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable PIX Payment'),
                        'name' => 'VENDOPAYMENT_PIX_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable if you want to show PIX payment on front'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable SEPA Payment'),
                        'name' => 'VENDOPAYMENT_SEPA_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable if you want to show SEPA payment on front'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable CRYPTO Payment'),
                        'name' => 'VENDOPAYMENT_CRYPTO_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable if you want to show CRYPTO payment on front'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),

                    // array(
                    //     'col' => 3,
                    //     'type' => 'text',
                    //     'desc' => $this->l('If the payment is successful, the payment provider or gateway redirects the customer back to the success URL'),
                    //     'name' => 'VENDOPAYMENT_ACCOUNT_SUCCESS_URL',
                    //     'label' => $this->l('Success URL'),
                    //     'required' => false, 
                    // ),
                    // array(
                    //     'col' => 3,
                    //     'type' => 'text',
                    //     'desc' => $this->l('The service sends an HTTP POST request to the post-back URL you provided.'),
                    //     'name' => 'VENDOPAYMENT_ACCOUNT_BACK_URL',
                    //     'label' => $this->l('Post Back URL'),
                    //     'required' => true, 
                    // ),
                    // array(
                    //     'col' => 3,
                    //     'type' => 'text',
                    //     'desc' => $this->l('Payment Title which display at front'),
                    //     'name' => 'VENDOPAYMENT_ACCOUNT_TITLE',
                    //     'label' => $this->l('Payment Title'),
                    // ),
                    // array(
                    //     'col' => 3,
                    //     'type' => 'text',
                    //     'desc' => $this->l('Payment Description which show at front'),
                    //     'name' => 'VENDOPAYMENT_ACCOUNT_DESCRIPTION',
                    //     'label' => $this->l('Payment Description'),
                    // ),
                    

                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Unique identifier or code associated with a particular website'),
                        'name' => 'VENDOPAYMENT_ACCOUNT_SITE_ID',
                        'label' => $this->l('Site ID'),
                        'required' => true, 
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('You can get your Merchant ID from Vendo backoffice'),
                        'name' => 'VENDOPAYMENT_ACCOUNT_MERCHANT_ID',
                        'label' => $this->l('Merchant ID'),
                        'required' => true, 
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('You can get your API Secret Live from Vendo backoffice'),
                        'name' => 'VENDOPAYMENT_ACCOUNT_SECRET_LIVE',
                        'label' => $this->l('API Secret Live'),
                        'required' => true, 
                    ),

                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('You can get your API Secret Test from Vendo backoffice'),
                        'name' => 'VENDOPAYMENT_ACCOUNT_SECRET_TEST',
                        'label' => $this->l('API Secret Test'),
                        'required' => true, 
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            
            'VENDOPAYMENT_LIVE_MODE' => Configuration::get('VENDOPAYMENT_LIVE_MODE', true),
            'VENDOPAYMENT_CARD_MODE' => Configuration::get('VENDOPAYMENT_CARD_MODE', true),
            'VENDOPAYMENT_PIX_MODE' => Configuration::get('VENDOPAYMENT_PIX_MODE', true),
            'VENDOPAYMENT_SEPA_MODE' => Configuration::get('VENDOPAYMENT_SEPA_MODE', true),
            'VENDOPAYMENT_CRYPTO_MODE' => Configuration::get('VENDOPAYMENT_CRYPTO_MODE', true),
            // // 'VENDOPAYMENT_ACCOUNT_SUCCESS_URL' => Configuration::get('VENDOPAYMENT_ACCOUNT_SUCCESS_URL', true),
            // 'VENDOPAYMENT_ACCOUNT_BACK_URL' => Configuration::get('VENDOPAYMENT_ACCOUNT_BACK_URL', true),
            'VENDOPAYMENT_ACCOUNT_SITE_ID' => Configuration::get('VENDOPAYMENT_ACCOUNT_SITE_ID', true),
            'VENDOPAYMENT_ACCOUNT_MERCHANT_ID' => Configuration::get('VENDOPAYMENT_ACCOUNT_MERCHANT_ID', true),
            'VENDOPAYMENT_ACCOUNT_SECRET_LIVE' => Configuration::get('VENDOPAYMENT_ACCOUNT_SECRET_LIVE', true),
            'VENDOPAYMENT_ACCOUNT_SECRET_TEST' => Configuration::get('VENDOPAYMENT_ACCOUNT_SECRET_TEST', true),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $errors = 0;
        $form_values = $this->getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            
            if ((Tools::getValue($key) == false || Tools::getValue($key) =="") && $key != 'VENDOPAYMENT_LIVE_MODE' && $key != 'VENDOPAYMENT_CARD_MODE' && $key != 'VENDOPAYMENT_PIX_MODE' && $key != 'VENDOPAYMENT_SEPA_MODE' && $key != 'VENDOPAYMENT_CRYPTO_MODE'){
                $errors = 1;
            }
            Configuration::updateValue($key, Tools::getValue($key));
        }
        return $errors;
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */


    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookActionOrderStatusUpdate(&$params)
    {
        $id_order = $params['id_order'];
        $new_order_status_id = $params['newOrderStatus']->id;

        $osCanceled = (int)Configuration::get('PS_OS_CANCELED');
        $osRefunded = (int)Configuration::get('PS_OS_REFUND');

        if ($params['newOrderStatus']->id == $osCanceled || $params['newOrderStatus']->id == $osRefunded) {
            $transection_id = $this->getOrderTransaction($id_order);
            if ($transection_id) {
                $this->generateRefund($transection_id);
            } else {
                return true;
            }
        }
    }

    public function generateRefund($transection_id)
    {
        include(dirname(_PS_MODULE_DIR_).'/modules/vendopayment/vendor/autoload.php');
        $sharedSecret = Configuration::get('VENDOPAYMENT_ACCOUNT_SECRET_LIVE');
        $merchant_id = Configuration::get('VENDOPAYMENT_ACCOUNT_MERCHANT_ID');
        try {
            $refund = new \VendoSdk\S2S\Request\Refund();
            $refund->setApiSecret($sharedSecret);
            $refund->setMerchantId($merchant_id);
            $refund->setIsTest(true);
            $refund->setTransactionId($transection_id);
            $response = $refund->postRequest();

            if ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_OK) {
                return 1;
            } elseif ($response->getStatus() == \VendoSdk\Vendo::S2S_STATUS_NOT_OK) {
                return 2;
            }
        } catch (\VendoSdk\Exception $exception) {
            return 2;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return 2;
        }
    }

    public function getOrderTransaction($id_order)
    {
        $order = new Order($id_order);
        $order_ref = $order->reference;
        $db = Db::getInstance();

        $sql = 'SELECT `transaction_id` 
                FROM `'._DB_PREFIX_.'order_payment` 
                WHERE `order_reference` = "'.$db->escape($order_ref).'"';

        $result = $db->getValue($sql);
        return $result;
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */

    public function hookModuleRoutes()
    {
        $lang = $this->context->language->id;
        $route_name = 'vendo_payment';
        $route_name2 = 'vendo_postback';
        
        return array(
            'module-' . $this->name . '-validatepay' => array(
                'controller' => 'validatepay',
                'rule' => $route_name,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
            'module-' . $this->name . '-postbackpay' => array(
                'controller' => 'postbackpay',
                'rule' => $route_name2,
                'keywords' => array(),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name,
                ),
            ),
        );
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookPaymentOptions($params)
    {
        $is_card = Configuration::get('VENDOPAYMENT_CARD_MODE');
        $is_pix = Configuration::get('VENDOPAYMENT_PIX_MODE');
        $is_sepa = Configuration::get('VENDOPAYMENT_SEPA_MODE');
        $is_crypto = Configuration::get('VENDOPAYMENT_CRYPTO_MODE');

        $cart = $this->context->cart;
        $action_url = $this->context->link->getModuleLink("vendopayment", "validatepay");
        
        $total_to_pay = (float)($params['cart']->getOrderTotal(true, Cart::BOTH));
        $customer = new Customer((int)($params['cart']->id_customer));
        $currency_order = new Currency($params['cart']->id_currency);
        $currency = $currency_order->iso_code;

        $total = $this->context->getCurrentLocale()->formatPrice(
            $params['cart']->getOrderTotal(true, Cart::BOTH),
            (new Currency($cart->id_currency))->iso_code
        );
        $taxLabel = '';
        if ($this->context->country->display_tax_label) {
            $taxLabel = $this->trans('(tax incl.)', [], 'Modules.Checkpayment.Admin');
        }

        $card_action = $this->context->link->getModuleLink("vendopayment", "validatecard");
        $pix_action = $this->context->link->getModuleLink("vendopayment", "validatepix");
        $sepa_action = $this->context->link->getModuleLink("vendopayment", "validatesepa");

        $this->smarty->assign('action',$action_url);
        $this->smarty->assign('card_action',$card_action);
        $this->smarty->assign('pix_action',$pix_action);
        $this->smarty->assign('sepa_action',$sepa_action);
        $this->smarty->assign('checkTotal',$total);
        $this->smarty->assign('checkTaxLabel',$taxLabel);
        $logo = _MODULE_DIR_ . 'vendopayment/views/img/small.png';
        $this->context->smarty->assign('logo', $logo);
        $token = Tools::getToken();
        
        if ($is_card == 1) {
            $newOption2 = new PaymentOption();
            $newOption2->setModuleName($this->name)
            ->setCallToActionText($this->l('Pay By Card'))
            ->setAction('#')
            ->setAdditionalInformation($this->fetch('module:vendopayment/views/templates/front/card_payment.tpl'));
            $payment_options[] = $newOption2;
        }
        if ($is_pix == 1) {
            $newOption3 = new PaymentOption();
            $paymentForm = $this->fetch('module:vendopayment/views/templates/front/payment_form.tpl');
            $newOption3->setModuleName($this->name)
            ->setCallToActionText($this->l('Pay By PIX'))
            ->setAction('#')
            ->setAdditionalInformation($this->fetch('module:vendopayment/views/templates/front/pix_payment.tpl'));
            
            $id_cart = $this->context->cart->id;
            $cart = new Cart($id_cart);
            $customer_address = new Address($cart->id_address_delivery);
            $id_country = $customer_address->id_country;
            $country_obj = new country($id_country);
            $iso_code = $country_obj->iso_code;
            
            if ($iso_code == 'BR') {
                $payment_options[] = $newOption3;
            }
            
        }
        if ($is_sepa == 1) {
            $newOption4 = new PaymentOption();
            $paymentForm = $this->fetch('module:vendopayment/views/templates/front/payment_form.tpl');
            $newOption4->setModuleName($this->name)
            ->setCallToActionText($this->l('Pay By SEPA'))
            ->setAction('#')
            ->setAdditionalInformation($this->fetch('module:vendopayment/views/templates/front/sepa_payment.tpl'));

            $payment_options[] = $newOption4;
        }
        if ($is_crypto == 1) {
            $newOption = new PaymentOption();
            $paymentForm = $this->fetch('module:vendopayment/views/templates/front/payment_form.tpl');
            $newOption->setModuleName($this->name)
                ->setCallToActionText($this->l('Pay By Crypto'))
                ->setAction($this->context->link->getModuleLink('vendopayment', 'validatepay', array('action' => 'crypto','token' => $token), true));
            $payment_options[] = $newOption;
        }

        return $payment_options;
    }

    public function installOrderState()
    {
        if (!Configuration::get('VENDO_OS_SUCCESS')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('VENDO_OS_SUCCESS')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'Vendo Payment Accepted';
                } else {
                    $order_state->name[$language['id_lang']] = 'Vendo Payment Accepted';
                }
            }
            $order_state->send_email = false;
            $order_state->color = '#3498D8';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = true;
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'vendopayment/views/img/small.png';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int)$order_state->id . '.gif';
                copy($source, $destination);
            }

            if (Shop::isFeatureActive()) {
                $shops = Shop::getShops();
                foreach ($shops as $shop) {
                    Configuration::updateValue('VENDO_OS_SUCCESS', (int) $order_state->id, false, null, (int)$shop['id_shop']);
                }
            } else {
                Configuration::updateValue('VENDO_OS_SUCCESS', (int) $order_state->id);
            }
        }

        if (!Configuration::get('VENDO_OS_PENDING')
            || !Validate::isLoadedObject(new OrderState(Configuration::get('VENDO_OS_PENDING')))) {
            $order_state = new OrderState();
            $order_state->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $order_state->name[$language['id_lang']] = 'Awaiting Vendo Payment';
                } else {
                    $order_state->name[$language['id_lang']] = 'Awaiting Vendo Payment';
                }
            }
            $order_state->send_email = false;
            $order_state->color = #34209E';
            $order_state->hidden = false;
            $order_state->delivery = false;
            $order_state->logable = false;
            $order_state->invoice = false;
            $order_state->module_name = $this->name;
            if ($order_state->add()) {
                $source = _PS_MODULE_DIR_ . 'vendopayment/views/img/small.png';
                $destination = _PS_ROOT_DIR_ . '/img/os/' . (int)$order_state->id . '.gif';
                copy($source, $destination);
            }

            if (Shop::isFeatureActive()) {
                $shops = Shop::getShops();
                foreach ($shops as $shop) {
                    Configuration::updateValue('VENDO_OS_PENDING', (int) $order_state->id, false, null, (int)$shop['id_shop']);
                }
            } else {
                Configuration::updateValue('VENDO_OS_PENDING', (int) $order_state->id);
            }
        }

        // if (!Configuration::get('VENDO_OS_REFUND')
        //     || !Validate::isLoadedObject(new OrderState(Configuration::get('VENDO_OS_REFUND')))) {
        //     $order_state = new OrderState();
        //     $order_state->name = array();
        //     foreach (Language::getLanguages() as $language) {
        //         if (Tools::strtolower($language['iso_code']) == 'fr') {
        //             $order_state->name[$language['id_lang']] = 'Refund Vendo Payment';
        //         } else {
        //             $order_state->name[$language['id_lang']] = 'Refund Vendo Payment';
        //         }
        //     }
        //     $order_state->send_email = false;
        //     $order_state->color = #F00';
        //     $order_state->hidden = false;
        //     $order_state->delivery = false;
        //     $order_state->logable = false;
        //     $order_state->invoice = false;
        //     $order_state->module_name = $this->name;
        //     if ($order_state->add()) {
        //         $source = _PS_MODULE_DIR_ . 'vendopayment/views/img/small.png';
        //         $destination = _PS_ROOT_DIR_ . '/img/os/' . (int)$order_state->id . '.gif';
        //         copy($source, $destination);
        //     }

        //     if (Shop::isFeatureActive()) {
        //         $shops = Shop::getShops();
        //         foreach ($shops as $shop) {
        //             Configuration::updateValue('VENDO_OS_REFUND', (int) $order_state->id, false, null, (int)$shop['id_shop']);
        //         }
        //     } else {
        //         Configuration::updateValue('VENDO_OS_REFUND', (int) $order_state->id);
        //     }
        // }
        return true;
    }
}
