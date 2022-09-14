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

class AstroPayPaymentsValidationModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        /**
         * Verify if this module is enabled and if the cart has
         * a valid customer, delivery address and invoice address
         */
        $cart = $this->context->cart;
        if (!$this->module->active || $cart->id_customer == 0 || $cart->id_address_delivery == 0
            || $cart->id_address_invoice == 0) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        /**
         * Check if this is a valid customer account
         * @var CustomerCore $customer
         */
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        /**
         * Verify if this payment module is authorized
         */
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'astropaypayments') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die($this->l('This payment method is not available.'));
        }

        /**
         * Create the order
         */
        $this->module->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_CHEQUE'),
            $cart->getOrderTotal(true, Cart::BOTH),
            $this->module->displayName,
            $this->module->l('Order initialized by AstroPay'),
            array(),
            $this->context->currency->id,
            false,
            $customer->secure_key
        );

        /**
         * If the order has been validated we try to retrieve it
         */
        $order_id = Order::getOrderByCartId((int) $cart->id);
        if (!$order_id) {
            die($this->module->l('Failed to create an order.'));
        }

        /**
         * Process the payment
         */
        $this->processPayment($order_id, $cart, $customer);
    }


    protected function processPayment($order_id, $cart, $customer)
    {
        /**
         * Create and store Secure Key for cart
         */
        $cart_id = (int) $cart->id;
        $secure_key = $customer->secure_key;
        $callback_key = Db::getInstance()->getValue('SELECT `callback_key` FROM ' . _DB_PREFIX_ .
            'astropay WHERE `id_cart`=' . $cart_id, 1);
        if (! $callback_key) {
            $callback_key = uniqid();
            $redirect_key = uniqid();
            Db::getInstance()->insert('astropay', array(
                'id_cart' => (int)$cart_id,
                'id_order' => (int)$order_id,
                'secure_key' => pSQL($secure_key),
                'callback_key' => pSQL($callback_key),
                'redirect_key' => pSQL($redirect_key),
            ));
        }
        $redirect_key = Db::getInstance()->getValue('SELECT `redirect_key` FROM ' . _DB_PREFIX_ .
            'astropay WHERE `id_cart`=' . $cart_id, 1);

        /**
         * Redirection URLs
         */
        $orderParams = array(
            'cart_id' => $cart_id,
            'order_id' => $order_id,
            'secure_key' => $secure_key,
        );
        $callbackParams = $orderParams + ['callback_key' => $callback_key, 'cb_type' => 'deposit'];
        $redirectParams = $orderParams + ['redirect_key' => $redirect_key];
        $callbackURL = $this->context->link->getModuleLink('astropaypayments', 'callback', $callbackParams, true);
        $redirectURL = $this->context->link->getModuleLink('astropaypayments', 'redirect', $redirectParams, true);

        /**
         * Validate parameters
         */
        $errors = [];
        $errorURL = $this->context->link->getPageLink('order');
        $currency = new CurrencyCore($cart->id_currency);
        if (!Validate::isLoadedObject($currency)) {
            $errors[] = $this->module->l('Failed to load currency from the cart.');
        }
        $address = new Address($cart->id_address_invoice);
        if (!Validate::isLoadedObject($address)) {
            $errors[] = $this->module->l('Failed to load address from the cart.');
        }
        $country = new Country($address->id_country);
        if (!Validate::isLoadedObject($country)) {
            $errors[] = $this->module->l('Failed to obtain country from the address.');
        }
        $order = new Order($order_id);
        if (!Validate::isLoadedObject($order)) {
            $errors[] = $this->module->l('Failed to find the order.');
        }
        if (!empty($errors)) {
            $this->errors[] = $errors;
            $this->redirectWithNotifications($errorURL);
        }

        /**
         * Obtain the payment particulars
         */
        $body = array(
            'amount' => round($cart->getOrderTotal(true, Cart::BOTH), 2),
            'currency' => $currency->iso_code,
            'country' => $country->iso_code,
            'merchant_deposit_id' => uniqid(),
            'callback_url' => $callbackURL,
            'redirect_url' => $redirectURL,
            'user' => array(
                'merchant_user_id' => $cart->id_customer,
                'email' => $customer->email,
                'phone' => $address->phone,
                'first_name' => $address->firstname,
                'last_name' => $address->lastname,
                'address' => array(
                    'line1' => $address->address1,
                    'line2' => $address->address2,
                    'city' => $address->city,
                    'country' => $country->iso_code,
                    'zip' => $address->postcode
                )
            ),
            'product' => array(
                'mcc' => 7995,
                'category' => 'eCommerce',
                'merchant_code' => 'eBusiness',
                'description' => 'eCommerce Business'
            ),
        );

        $state = $address->id_state ? new State($address->id_state) : null;
        if ($state) {
            $body['user']['address']['province'] = $state->iso_code;
        }

        /**
         * Obtain the visual info and payment method
         */
        $payment_method = Tools::getValue('apcpm');
        if (!empty($payment_method)) {
            $body['payment_method_code'] = $payment_method;
        }

        $merchant_name = Configuration::get('ASTROPAY_MERCHANT_NAME', null);
        $logo_url = Configuration::get('ASTROPAY_MERCHANT_LOGO_URL', null);

        if (!empty($merchant_name) || !empty($logo_url)) {
            $body['visual_info'] = [];
            if (!empty($merchant_name)) {
                $body['visual_info']['merchant_name'] = $merchant_name;
            }
            if (!empty($logo_url)) {
                $body['visual_info']['merchant_logo'] = $logo_url;
            }
        }

        /**
         * Initiate the payment
         */
        try {
            $api_url = 'merchant/v1/deposit/init';
            $data = AstroPayUtility::postData($api_url, $body);
            $url = $data['url'] ?? false;
            $merchant_deposit_id = $data['merchant_deposit_id'] ?? null;
            $deposit_external_id = $data['deposit_external_id'] ?? null;
            $status = $data['status'] ?? 'PENDING';
            if ($url) {
                Db::getInstance()->update('astropay', array(
                    'merchant_deposit_id' => pSQL($merchant_deposit_id),
                    'deposit_external_id' => pSQL($deposit_external_id),
                    'deposit_status' => pSQL($status),
                ), '`id_cart`=' . $cart_id);
                Tools::redirectLink($url);
            }
        } catch (\Exception $e) {
            $this->errors[] = $this->module->l($e->getMessage());
            $this->redirectWithNotifications($errorURL);
        }
    }
}
