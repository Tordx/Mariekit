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
    require_once(dirname(__FILE__) . '/utility/astropay-utility.php');
}

class AstroPayPayments extends PaymentModule
{
    protected $config_form = false;

    public function __construct()
    {
        $this->module_key = '69eb2b1888e8b1e7fd47e0b01275f706';
        $this->name = 'astropaypayments';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'SkillsUp';

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->is_eu_compatible = 1;

        $this->bootstrap = true;

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->controllers = array('validation', 'redirect', 'callback');

        $this->displayName = $this->l('AstroPay for Prestashop');
        $this->description = $this->l(
            'Accept local payment methods using your AstroPay Merchant account.'
        );

        parent::__construct();
    }

    /**
     * Don't forget to create update methods if needed:
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        require(dirname(__FILE__) . '/sql/install.php');

        Configuration::updateValue('ASTROPAY_TEST_MODE', false);
        Configuration::updateValue('ASTROPAY_API_KEY', null);
        Configuration::updateValue('ASTROPAY_SECRET', null);
        Configuration::updateValue('ASTROPAY_MERCHANT_NAME', null);
        Configuration::updateValue('ASTROPAY_MERCHANT_LOGO_URL', null);

        return parent::install() &&
            $this->registerHook('paymentOptions') &&
            $this->registerHook('actionOrderSlipAdd');
    }

    public function uninstall()
    {
        Configuration::deleteByName('ASTROPAY_TEST_MODE');
        Configuration::deleteByName('ASTROPAY_API_KEY');
        Configuration::deleteByName('ASTROPAY_SECRET');
        Configuration::deleteByName('ASTROPAY_MERCHANT_NAME');
        Configuration::deleteByName('ASTROPAY_MERCHANT_LOGO_URL');

        require(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitAstroPayModule')) == true) {
            $TEST_MODE = Tools::getValue('ASTROPAY_TEST_MODE');
            $api_key = Tools::getValue('ASTROPAY_API_KEY');
            $secret_key = Tools::getValue('ASTROPAY_SECRET');
            $merchant_name = Tools::getValue('ASTROPAY_MERCHANT_NAME');
            $logo_url = filter_var(
                Tools::getValue('ASTROPAY_MERCHANT_LOGO_URL'),
                FILTER_VALIDATE_URL,
                FILTER_FLAG_PATH_REQUIRED|FILTER_NULL_ON_FAILURE
            );
            if (empty($api_key)) {
                $output .= $this->displayError($this->l('Merchant Gateway API Key is required'));
            } elseif (empty($secret_key)) {
                $output .= $this->displayError($this->l('Secret Key is required'));
            } else {
                Configuration::updateValue('ASTROPAY_TEST_MODE', $TEST_MODE);
                Configuration::updateValue('ASTROPAY_API_KEY', $api_key);
                Configuration::updateValue('ASTROPAY_SECRET', $secret_key);
                Configuration::updateValue('ASTROPAY_MERCHANT_NAME', $merchant_name);
                Configuration::updateValue('ASTROPAY_MERCHANT_LOGO_URL', $logo_url);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        return $output . $this->renderForm();
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
        $helper->submit_action = 'submitAstroPayModule';
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
                        'hint' => $this->l('Enable test Mode. If enabled, use test keys below.'),
                        'name' => 'ASTROPAY_TEST_MODE',
                        'label' => $this->l('Test Mode'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-sitemap"></i>',
                        'desc' => $this->l('Enter a valid API key for selected mode'),
                        'name' => 'ASTROPAY_API_KEY',
                        'label' => $this->l('Merchant Gateway API Key'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Enter a valid secret key for selected mode'),
                        'name' => 'ASTROPAY_SECRET',
                        'label' => $this->l('Secret Key'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-header"></i>',
                        'desc' => $this->l('Merchant name that would appear on payment screen'),
                        'name' => 'ASTROPAY_MERCHANT_NAME',
                        'label' => $this->l('Merchant Name (Optional)'),
                        'required' => false,
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-image"></i>',
                        'desc' => $this->l('Merchant Logo image that would appear on payment screen'),
                        'name' => 'ASTROPAY_MERCHANT_LOGO_URL',
                        'label' => $this->l('Merchant Logo URL (Optional)'),
                        'required' => false,
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
            'ASTROPAY_TEST_MODE' => Configuration::get('ASTROPAY_TEST_MODE', false),
            'ASTROPAY_API_KEY' => Configuration::get('ASTROPAY_API_KEY', null),
            'ASTROPAY_SECRET' => Configuration::get('ASTROPAY_SECRET', null),
            'ASTROPAY_MERCHANT_NAME' => Configuration::get('ASTROPAY_MERCHANT_NAME', null),
            'ASTROPAY_MERCHANT_LOGO_URL' => Configuration::get('ASTROPAY_MERCHANT_LOGO_URL', null),
        );
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
     * @return array|null
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $option->setCallToActionText($this->l('Pay via AstroPay'))
            ->setForm($this->generateForm())
            ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/logo-small.png'));

        return [
            $option
        ];
    }

    /**
     * Generates payment methods selector for payment form
     */
    protected function generateForm()
    {
        try {
            $cart = $this->context->cart;
            $address = new Address($cart->id_address_invoice);
            $country = new Country($address->id_country);
            $country_code = $country->iso_code;
            $path = 'merchant/v1/paymentMethods?country=' . $country_code;
            $data = AstroPayUtility::getData($path);
            $payment_methods = $data['paymentMethods'];
        } catch (\Exception $e) {
            PrestaShopLogger::addLog('Error while generating payment form: ' . $e->getMessage(), 3);
            return;
        }

        $this->context->smarty->assign([
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
            'all_methods' => AstroPayUtility::PAYMENT_METHODS,
            'payment_methods' => $payment_methods,
            'img_base' => Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/views/img/payment')
        ]);

        return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/front/payment_methods.tpl');
    }

    /**
     * Handle refunds for credit slips
     *
     * @param array Hook parameters
     *
     * @return bool|null
     */
    public function hookActionOrderSlipAdd($params)
    {
        /**
         * Process only if the module is active and order was paid via this module
         */
        if (!$this->active) {
            return;
        }

        $order = $params['order'];
        if ($this->name != $order->module) {
            return;
        }

        try {
            /**
             * Submit the refund request and update status
             */
            $data = $this->processRefund($order);
            $status = $data['status'];
            $cashout_id = $data['cashout_id'];
            Db::getInstance()->update('astropay', array(
                'cashout_external_id' => pSQL($cashout_id),
                'refund_status' => pSQL($status),
            ), '`id_cart`=' . (int) $order->id_cart);
            if ('APPROVED' == $status) {
                $order->setCurrentState(Configuration::get('PS_OS_REFUND'));
            }
            PrestaShopLogger::addLog("A refund with cashout id $cashout_id has been created with status $status for " .
                "order {$order->reference}");
            return 'CANCELLED' != $status;
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->context->controller->errors[] = $this->l($error);
            PrestaShopLogger::addLog("Error while processing refund for order {$order->reference}: $error", 3);
            return false;
        }
    }

    private function processRefund($order)
    {
        /**
         * Validate order was paid via AstroPay
         */
        $cart_id = (int) $order->id_cart;

        $deposit_status = Db::getInstance()->getValue('SELECT `deposit_status` FROM ' . _DB_PREFIX_ .
            'astropay WHERE `id_cart`=' . $cart_id, 1);
        if ('APPROVED' != $deposit_status) {
            throw new \Exception('Failed to locate AstroPay payment record for this order.');
        }

        /**
         * Obtain the credit slip
         */
        $orderSlip = $order->getOrderSlipsCollection()
                           ->orderBy('date_upd', 'desc')
                           ->getFirst();
        if (! $orderSlip) {
            throw new \Exception('Refund was made without a credit slip.');
        }

        /**
         * Obtain the refund amount
         */
        $amount = $orderSlip->amount;
        if ($orderSlip->shipping_cost === '1') {
            $amount += $orderSlip->shipping_cost_amount;
        }

        if ($amount <= 0) {
            throw new \Exception('Refund amount must be greater than 0.');
        }

        /**
         * Get various details
         */
        $customer_id = $order->id_customer;
        $customer = new Customer($customer_id);
        if (!Validate::isLoadedObject($customer)) {
            throw new \Exception('Failed to obtain customer from the order.');
        }

        $address = new Address($order->id_address_invoice);
        if (!Validate::isLoadedObject($address)) {
            throw new \Exception('Failed to obtain address from the order.');
        }

        $country = new Country($address->id_country);
        if (!Validate::isLoadedObject($country)) {
            throw new \Exception('Failed to obtain country from the address.');
        }

        $currency = new CurrencyCore($order->id_currency);
        if (!Validate::isLoadedObject($currency)) {
            throw new \Exception('Failed to obtain currency from the order.');
        }

        /**
         * Create a new cashout id
         */
        $cashout_id = uniqid();
        Db::getInstance()->update('astropay', array(
            'merchant_cashout_id' => pSQL($cashout_id),
        ), '`id_cart`=' . $cart_id);

        /**
         * Build callback parameters
         */
        $callback_key = Db::getInstance()->getValue('SELECT `callback_key` FROM ' . _DB_PREFIX_ .
            'astropay WHERE `id_cart`=' . $cart_id, 1);
        $callbackParams = array(
            'cart_id' => $cart_id,
            'secure_key' => $order->secure_key,
            'callback_key' => $callback_key,
            'cb_type' => 'refund',
        );
        $callbackURL = $this->context->link->getModuleLink('astropaypayments', 'callback', $callbackParams, true);

        /**
         * Build request data array
         */
        $data = array(
            'amount' => round($amount, 2),
            'currency' => $currency->iso_code,
            'country' => $country->iso_code,
            'merchant_cashout_id' => $cashout_id,
            'callback_url' => $callbackURL,
            'user' => array(
                'merchant_user_id' => $customer_id,
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
        );

        $merchant_name = Configuration::get('ASTROPAY_MERCHANT_NAME', null);
        $logo_url = Configuration::get('ASTROPAY_MERCHANT_LOGO_URL', null);

        if (!empty($merchant_name) || !empty($logo_url)) {
            $data['visual_info'] = [];
            if (!empty($merchant_name)) {
                $data['visual_info']['merchant_name'] = $merchant_name;
            }
            if (!empty($logo_url)) {
                $data['visual_info']['merchant_logo'] = $logo_url;
            }
        }

        /**
         * Submit cashout request
         */
        return AstroPayUtility::postData('merchant/v1/cashout', $data);
    }
}
