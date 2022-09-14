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

class AstroPayPaymentsCallbackModuleFrontController extends ModuleFrontController
{
    public function run()
    {
        /**
         * Boot the kernel
         */
        if (_PS_VERSION_ > '1.7.6.0') {
            global $kernel;
            if (!$kernel) {
                require_once(_PS_ROOT_DIR_.'/app/AppKernel.php');
                $kernel = new \AppKernel('prod', !Configuration::get('ASTROPAY_TEST_MODE', false));
                $kernel->boot();
            }
        }

        /**
         * Obtain the order id and callback type
         */
        try {
            $cart_id = (int) Tools::getValue('cart_id');
            $cb_type = Tools::getValue('cb_type');

            /**
             * Parse the posted data
             */
            $input = Tools::file_get_contents('php://input');
            $data = json_decode($input, true);
            if (!$data) {
                parse_str($input, $data);
            }
            PrestaShopLogger::addLog("Recevied $cb_type callback for cart id $cart_id with data: " .
                var_export($data, true));

            /**
             * Validate the data
             */
            if (empty($data)) {
                throw new \Exception("No data recived in callback.");
            }

            /**
             * Validate the AstroPay record
             */
            $astropay = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'astropay WHERE `id_cart`=' .
                $cart_id, 1);
            if (empty($astropay)) {
                throw new \Exception("Could not find AstroPay record.");
            }

            /**
             * Validate secure key and callback key
             */
            $secure_key = Tools::getValue('secure_key');
            if (empty($secure_key) || $secure_key != $astropay['secure_key']) {
                throw new \Exception("Failed to validate order secure key.");
            }
            $callback_key = Tools::getValue('callback_key');
            if (empty($callback_key) || $callback_key != $astropay['callback_key']) {
                throw new \Exception("Failed to validate payment gateway callback key.");
            }

            /**
             * Process deposit or refund callback
             */
            if ('deposit' == $cb_type) {
                $this->processDeposit($cart_id, $data, $astropay);
            } else {
                $this->processRefund($cart_id, $data, $astropay);
            }
            die(true);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            PrestaShopLogger::addLog("In $cb_type callback for cart id $cart_id: $error", 2);
            die($error);
        }
    }

    private function processDeposit($cart_id, $data, $astropay)
    {
        $order_id = Tools::getValue('order_id');

        /**
         * Validate the order ID and deposit ID
         */
        if ($order_id != $astropay['id_order']) {
            throw new \Exception("Order id mismatch.");
        }
        if ($astropay['deposit_external_id'] != $data['deposit_external_id']) {
            throw new \Exception("Deposit id mismatch.");
        }

        /**
         * Ignore if the status is already processed
         */
        if ($astropay['deposit_status'] == $data['status']) {
            throw new \Exception("Ignoring already processed deposit status - {$data['status']}");
        }

        /**
         * Update the order status as appropriate
         */
        $deposit_status = $data['status'];
        Db::getInstance()->update('astropay', array(
            'deposit_status' => pSQL($deposit_status),
        ), '`id_cart`=' . (int) $cart_id);
    
        $order = new Order($order_id);
        if ('APPROVED' == $deposit_status) {
            $paid = Configuration::get('PS_OS_PAYMENT');
            if ($paid !== $order->getCurrentState()) {
                $order->setCurrentState($paid);
            }
        } elseif ('CANCELLED' == $deposit_status) {
            $order->setCurrentState(Configuration::get('PS_OS_CANCELED'));
        }
    }

    private function processRefund($cart_id, $data, $astropay)
    {
        /**
         * Validate the cashout id
         */
        if ($astropay['merchant_cashout_id'] != $data['merchant_cashout_id']) {
            throw new \Exception("Cashout id mismatch.");
        }

        /**
         * Ignore if the status is already processed
         */
        if ($astropay['refund_status'] == $data['status']) {
            throw new \Exception("Ignoring already processed refund status - {$data['status']}");
        }

        /**
         * Update the order status as appropriate
         */
        $refund_status = $data['status'];
        Db::getInstance()->update('astropay', array(
            'refund_status' => pSQL($refund_status),
        ), '`id_cart`=' . (int) $cart_id);

        if ('APPROVED' == $refund_status) {
            $order = new Order($astropay['id_order']);
            $order->setCurrentState(Configuration::get('PS_OS_REFUND'));
        }
    }
}
