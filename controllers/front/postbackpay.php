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

class VendopaymentPostbackPayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // $jsonn = file_get_contents('php://input');
        // $data = json_decode($jsonn, true);
        $html =json_encode($_POST);
        $transaction_id = $_POST['transaction_id'];
        $callback = $_POST['callback'];
        $payment_method_id = $_POST['payment_method_id'];
        $transaction_status = $_POST['transaction_status'];

        // $ttoken='1221';
        // $transaction_id = 'sss';
        // $type='';
        // $id_cart=11;
        // $id_customer=22;
        $status=1;
        // Db::getInstance()->insert('vendopayment', array('token'=>pSQL($ttoken),
        //         'transaction_id'=>pSQL($transaction_id),
        //         'type'=>pSQL($type),
        //         'id_cart'=>pSQL($id_cart),'html'=>$html, 'id_customer'=>pSQL($id_customer)));

        if ($callback) {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <postbackResponse>
                <'.$callback.'>
                    <code>'.$status.'</code>
                </'.$callback.'>
            </postbackResponse>';

            header('Content-Type: application/xml');
            
            echo $xml;
        } else {
            dump('call back url call ...');
        }
        exit();
    }
}
