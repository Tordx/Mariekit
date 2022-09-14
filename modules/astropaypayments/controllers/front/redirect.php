<?php
/**
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

if (!class_exists('AstroPayUtility')) {
    require_once(dirname(__FILE__) . '/../../utility/astropay-utility.php');
}

class AstroPayPaymentsRedirectModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        try {
            $cart_id = (int) Tools::getValue('cart_id');
            $order_id = Tools::getValue('order_id');
            $secure_key = Tools::getValue('secure_key');
            $redirect_key = Tools::getValue('redirect_key');

            /**
             * Find the astropay entry for the order
             */
            $astropay = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'astropay WHERE `id_cart`=' .
                $cart_id, 1);
            if (empty($astropay)) {
                throw new \Exception('Failed to locate AstroPay payment record for the order');
            }
    
            /**
             * Validate various keys
             */
            if (empty($secure_key) || $secure_key != $astropay['secure_key']) {
                throw new \Exception('Failed to validate order secure key.');
            }
            if (empty($redirect_key) || $redirect_key != $astropay['redirect_key']) {
                throw new \Exception('Failed to validate payment gateway redirect key.');
            }

            /**
             * Check the latest deposit status
             */
            $deposit_status = $astropay['deposit_status'];
            if ('PENDING' == $deposit_status) {
                $deposit_id = $astropay['deposit_external_id'];
                $api_path = 'merchant/v1/deposit/' . $deposit_id . '/status';
                $data = AstroPayUtility::getData($api_path);
                $deposit_status = $data['status'];
                Db::getInstance()->update('astropay', array(
                    'deposit_status' => pSQL($deposit_status),
                ), '`id_cart`=' . $cart_id);
            }

            $order = new Order($order_id);
            $order_ref = $order->reference;

            if ('APPROVED' == $deposit_status) {
                /**
                 * The order has been placed so we redirect the customer on the confirmation page.
                 */
                $order->setCurrentState(Configuration::get('PS_OS_PAYMENT'));
                $module_id = $this->module->id;
                Tools::redirect(
                    'index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $module_id .
                    '&id_order=' . $order_id . '&key=' . $secure_key
                );
            } elseif ('CANCELLED' == $deposit_status) {
                /**
                 * Deposit has been cancelled, so cancel the order
                 */
                $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
                throw new \Exception("The order with reference $order_ref has been cancelled.");
            } else {
                /**
                 * Depsit is yet to be completed
                 */
                throw new \Exception("Your order has been created with reference $order_ref, but the deposit is yet " .
                    "to be completed. Current deposit status is $deposit_status. The order will be confirmed after " .
                    "deposit completion.");
            }
        } catch (\Exception $e) {
            /**
             * Send the customer to Order History page
             */
            $error = $e->getMessage();
            $this->errors[] = $this->module->l($error);
            $this->redirectWithNotifications('index.php?controller=order-confirmation');
        }
    }
}
