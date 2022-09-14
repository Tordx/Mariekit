<?php
/**
 * @author   Twispay
 * @version  1.3.0
 */

require _PS_MODULE_DIR_.'twispay/classes/Twispay_Encoder.php';
require _PS_MODULE_DIR_.'twispay/classes/Twispay_Logger.php';
require _PS_MODULE_DIR_.'twispay/classes/Twispay_Response.php';
require _PS_MODULE_DIR_.'twispay/classes/Twispay_Status_Updater.php';
require _PS_MODULE_DIR_.'twispay/classes/Twispay_Transactions.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class Twispay extends PaymentModule
{
    protected $config_form = false;
    /**
    * Create Module
    */
    public function __construct()
    {
        /** Initialize module members */
        $this->name = 'twispay';
        $this->tab = 'payments_gateways';
        $this->version = '1.3.0';
        $this->author = 'Twispay';
        $this->need_instance = 0;
        $this->module_key = 'd89110977c71a97d064d510cc90d760c';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Credit card payments by Twispay');
        $this->description = $this->l('Module for Twispay payment gateway. Your customers can now pay with credit card.');
    }

    public function install()
    {
        Configuration::updateValue('TWISPAY_LIVE_MODE', false);
        Twispay_Transactions::createTransactionsTable();
        Twispay_Logger::makeLogDir();

        $return = parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayPaymentReturn') &&
            $this->registerHook('displayAdminOrderLeft') &&
            $this->registerHook('actionOrderStatusUpdate');

        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $return &= $this->registerHook('paymentOptions');
        } else {
            $return &= $this->registerHook('displayPayment');
        }

        return $return;
    }

    public function uninstall()
    {
        Configuration::deleteByName('TWISPAY_LIVE_MODE');
        Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'twispay_transactions`');
        Twispay_Logger::delLogDir();
        return parent::uninstall();
    }

    /**  Load the admin configuration page. */
    public function getContent()
    {
        /** If values have been submitted in the form, process.*/
        $messages = "";
        if (((bool)Tools::isSubmit('submitTwispayModule')) == true) {
            $post = $this->postProcess();
            if ($post === true) {
                $messages = $this->displayConfirmation($this->l('Settings have been saved.'));
            } elseif ($post === false) {
                $messages = $this->displayError($this->l('There was an error'));
            } else {
                $messages = $this->displayError($post);
            }
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        /** Load the transaction list */
        $output = $this->renderTransactionsList();
        /** Load the configuration form */
        return $messages.$this->renderForm().$output;
    }

    /**
    * Read configuration
    */
    /** Method for gettings keys info (siteId and privateKey) */
    public static function getKeysInfo()
    {
        if (Configuration::get('TWISPAY_LIVE_MODE')) {
            $info = array(
                'liveMode' => Configuration::get('TWISPAY_LIVE_MODE'),
                'privateKey' => Configuration::get('TWISPAY_PRIVATEKEY_LIVE'),
                'siteId' => Configuration::get('TWISPAY_SITEID_LIVE'),
                'formUrl' => 'https://secure.twispay.com',
            );
        } else {
            $info = array(
                'liveMode' => Configuration::get('TWISPAY_LIVE_MODE'),
                'privateKey' => Configuration::get('TWISPAY_PRIVATEKEY_STAGING'),
                'siteId' => Configuration::get('TWISPAY_SITEID_STAGING'),
                'formUrl' => 'https://secure-stage.twispay.com',
            );
        }
        if (!$info['privateKey'] || !$info['siteId']) {
            return false;
        }
        return $info;
    }

    /** Set values for the inputs.*/
    protected function getConfigFormValues()
    {
        return array(
            'TWISPAY_LIVE_MODE' => Configuration::get('TWISPAY_LIVE_MODE'),
            'TWISPAY_SITEID_STAGING' => Configuration::get('TWISPAY_SITEID_STAGING'),
            'TWISPAY_PRIVATEKEY_STAGING' => Configuration::get('TWISPAY_PRIVATEKEY_STAGING'),
            'TWISPAY_SITEID_LIVE' => Configuration::get('TWISPAY_SITEID_LIVE'),
            'TWISPAY_PRIVATEKEY_LIVE' => Configuration::get('TWISPAY_PRIVATEKEY_LIVE'),
            'TWISPAY_NOTIFICATION_URL' => $this->context->link->getModuleLink('twispay', 'validation'),
        );
    }

    /**
    * Create Admin pannel interface
    */
    /** Create the form that will be displayed in the configuration of your module.*/
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitTwispayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /** Create the structure of your form.*/
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Twispay settings'),
                'icon' => 'icon-cogs',
                ),
                /** Form fields */
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'TWISPAY_LIVE_MODE',
                        'is_bool' => false,
                        'desc' => $this->l('Select "YES" if you wish to use the payment gateway in Production or "No" if you want to use it in staging mode.'),
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
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-lock"></i>',
                        'desc' => $this->l('Enter the SITE ID for staging mode'),
                        'name' => 'TWISPAY_SITEID_STAGING',
                        'label' => $this->l('Staging Site ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Enter the Private key for staging mode'),
                        'name' => 'TWISPAY_PRIVATEKEY_STAGING',
                        'label' => $this->l('Staging Private key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-lock"></i>',
                        'desc' => $this->l('Enter the SITE ID for live mode'),
                        'name' => 'TWISPAY_SITEID_LIVE',
                        'label' => $this->l('Live Site ID'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-key"></i>',
                        'desc' => $this->l('Enter the Private key for live mode'),
                        'name' => 'TWISPAY_PRIVATEKEY_LIVE',
                        'label' => $this->l('Live Private key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-link"></i>',
                        'desc' => $this->l('Put this URL in your twispay account'),
                        'name' => 'TWISPAY_NOTIFICATION_URL',
                        'label' => $this->l('Server-to-server notification URL'),
                        'readonly' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /** Save form data.*/
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        $success = true;

        foreach (array_keys($form_values) as $key) {
            if (!Configuration::updateValue($key, Tools::getValue($key))) {
                $success = false;
            }
        }
        return $success;
    }

    /** Define the fields of Transactions List */
    public function renderTransactionsList()
    {
        $this->fields_list = array(
            'id_transaction' => array(
                'title' => $this->l('ID'),
                'type' => 'text',
                'search' => false,
            ),
            'order_reference' => array(
                'title' => $this->l('Order reference'),
                'type' => 'text',
                'search' => false,
            ),
            'customer_name' => array(
                'title' => $this->l('Customer name'),
                'type' => 'text',
                'search' => false,
            ),
            'transactionId' => array(
                'title' => $this->l('Transaction ID'),
                'type' => 'text',
                'search' => false,
            ),
            'transactionKind' => array(
                'title' => $this->l('Transaction Kind'),
                'type' => 'text',
                'search' => false,
            ),
            'amount_formatted' => array(
                'title' => $this->l('Amount'),
                'type' => 'text',
                'search' => false,
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'text',
                'search' => false,
            ),
            'date' => array(
                'title' => $this->l('Date'),
                'type' => 'text',
                'search' => false,
            ),
        );
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;

        /** Actions to be displayed in the "Actions" column */
        $helper->identifier = 'id_transaction';
        $helper->show_toolbar = true;
        $helper->title = $this->l('Transactions list');
        $helper->table = 'twispay_transactions';
        $helper->listTotal = Twispay_Transactions::getTransactionsNumber();
        $helper->_default_pagination = 20;
        $helper->simple_header = false;
        $page = (int)Tools::getValue('submitFilter'.$helper->table);
        $selected_pagination = Tools::getValue(
            $helper->table.'_pagination',
            isset($this->context->cookie->{$helper->table.'_pagination'}) ? $this->context->cookie->{$helper->table.'_pagination'} : $helper->_default_pagination
        );

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper->generateList(Twispay_Transactions::getTransactions($page, $selected_pagination), $this->fields_list);
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     * Display custom persistent errors
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJquery();
            Media::addJsDef(array(
                'TWISPAY_LIVE_MODE' => Configuration::get('TWISPAY_LIVE_MODE'),
            ));
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        if ($this->context->cookie->redirect_error) {
            /** Display persistent error */
            $this->context->controller->errors[] = $this->context->cookie->redirect_error;
            /** Clean persistent error */
            unset($this->context->cookie->redirect_error);
        }
    }

    /** Display last order information on Admin > Orders > Order */
    public function hookDisplayAdminOrderLeft($params)
    {
        $id_order = (int)$params['id_order'];
        $data = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'twispay_transactions`
        WHERE `id_cart` = (SELECT `id_cart` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` = "'.$id_order.'") ORDER BY `date` DESC');
        if (!$data) {
            return false;
        } else {
            /** Link the info to query */
            return $this->buildOrderMessage($data);
        }
    }

    /** Load template with assigned data */
    public function buildOrderMessage($data)
    {
        $this->context->smarty->assign('data', $data);
        return $this->display(__FILE__, 'views/templates/admin/payment_message.tpl');
    }

    /**
    * Create Front interface
    */
    /** Hook for order status update action */
    public function hookActionOrderStatusUpdate($params)
    {
        $status = $params['newOrderStatus']?$params['newOrderStatus']->id:false;

        if ($status === 7/** Refund status id */) {
            /** If transaction is registred in twispay transactions list */
            $transaction = Twispay_Transactions::getTransactionByCartId($params['cart']->id);
            if ($transaction) {
                if ($transaction['status'] == Twispay_Status_Updater::$RESULT_STATUSES['REFUND_OK']) {
                    Twispay_Logger::api_log($this->l('Order already refunded.'));
                    $this->context->cookie->redirect_error = $this->l('Order already refunded.');
                } else {
                    $keys = self::getKeysInfo();
                    if (!$keys) {
                        $this->context->cookie->redirect_error = $this->l('Twispay refund error: ').$this->l('Invalid API Keys.');
                        /** Redirect to order page */
                        /** Skip the part when the status is set */
                        Tools::redirect($_SERVER['HTTP_REFERER']);
                        die();
                    }
                    $refund = Twispay_Transactions::refundTransaction($transaction, $keys, $this);
                    if ($refund['refunded']) {
                        Twispay_Logger::api_log($this->l('Successfully refunded ').json_encode($refund));
                    } else {
                        Twispay_Logger::api_log($this->l('Twispay refund error: ').json_encode($refund));
                        $this->context->cookie->redirect_error = $this->l('Twispay refund error: ').$refund['status'];
                        /** Redirect to order page */
                        /** Skip the part when the status is set */
                        Tools::redirect($_SERVER['HTTP_REFERER']);
                        die();
                    }
                }
                /** If the order was not payed via twispay */
            } else {
                Twispay_Logger::api_log($this->l('Twispay refund error: ').$this->l('No transactions were found for order with id ').$params['id_order']);
                $this->context->cookie->redirect_error = $this->l('Twispay refund error: ').$this->l('No transactions were found for this order.');
            }
        }
    }

    /** Add Twispay payment option on checkout - Prestashop 1.6 */
    public function hookDisplayPayment($params)
    {
        if (!$this->active || !self::getKeysInfo()) {
            return;
        }

        $this->smarty->assign(
            $this->getPaymentVars($params)
        );

        $this->smarty->assign(array(
            'logos_folder' => _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/'.$this->name.'/views/img/',
        ));

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /** Add Twispay payment option on checkout - Prestashop 1.7 */
    public function hookPaymentOptions($params)
    {
        if (!$this->active || !self::getKeysInfo() || !class_exists('PrestaShop\PrestaShop\Core\Payment\PaymentOption')) {
            return;
        }

        $this->smarty->assign(
            $this->getPaymentVars($params)
        );

        $this->smarty->assign(array(
            'logos_folder' => _PS_BASE_URL_SSL_.__PS_BASE_URI__.'modules/'.$this->name.'/views/img/',
        ));

        $newOption = new PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $newOption->setModuleName($this->name)
            ->setCallToActionText($this->l('Pay by credit or debit card'))
            ->setForm($this->fetch('module:twispay/views/templates/hook/twispay_payment_form.tpl'))
            ->setAdditionalInformation($this->fetch('module:twispay/views/templates/hook/twispay_payment_extra.tpl'));
        $payment_options = array(
            $newOption,
        );

        return $payment_options;
    }

    /** Include files in front header */
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
    }

    /** Display order info in confirmation page */
    public function hookDisplayPaymentReturn($params)
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>') || $this->active == false) {
            return false;
        }

        $order = $params['objOrder'];

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR')) {
            $this->smarty->assign('status', 'ok');
        }
        $this->smarty->assign(array(
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
        ));

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }

    /**
    * Build POST message
    */
    public function getPaymentVars($params = false)
    {
        if (!$params) {
            $params = array();
            $params['cookie'] = $this->context->cookie;
            $params['cart'] = $this->context->cart;
        }

        /* Customer data */
        $customer_inputs = array();
        $customerPrefix = "p";
        $customer_inputs['identifier'] = $this->buildCustomerId($customerPrefix,$params['cart']->id_customer);
        $customerObj = new Customer((int)$params['cart']->id_customer);
        if (Validate::isLoadedObject($customerObj)) {
            $customer_inputs['firstName'] = $customerObj->firstname;
            $customer_inputs['lastName'] = $customerObj->lastname;
        }

        /** Customer address data */
        $id_address = (int)$params['cart']->id_address_invoice;
        if ($id_address) {
            $addressObj = new Address($id_address);
            /** Check if address is valid */
            if (Validate::isLoadedObject($addressObj)) {
                /** Check if country is valid */
                $countryObj = new Country($addressObj->id_country);
                if (Validate::isLoadedObject($countryObj)) {
                    $customer_inputs['country'] = $countryObj->iso_code;
                    /** Check if state is valid */
                    /** Only for US */
                    if ((int)$addressObj->id_state && $customer_inputs['country'] == 'US') {
                        $state = new State($addressObj->id_state);
                        if (Validate::isLoadedObject($state)) {
                            $customer_inputs['state'] = $state->iso_code;
                        }
                    }
                }
                $customer_inputs['city'] = $addressObj->city;
                $customer_inputs['zipCode'] = $addressObj->postcode;
                $customer_inputs['address'] = $addressObj->address1;
                if ($addressObj->address2) {
                    $customer_inputs['address'] .= " ".$addressObj->address2;
                }
                if ($addressObj->phone_mobile) {
                    $customer_inputs['phone'] = $addressObj->phone_mobile;
                } elseif ($addressObj->phone) {
                    $customer_inputs['phone'] = $addressObj->phone;
                }
                $customer_inputs['phone'] = ((strlen($customer_inputs['phone']) && $customer_inputs['phone'][0] == '+') ? ('+') : ('')) . preg_replace('/([^0-9]*)+/', '', $customer_inputs['phone']);

                $customer_inputs['email'] = $customerObj->email;
            }
        }

        /** Items details data */
        $cart = $params['cart'];
        $products = $cart->getProducts();
        $items = array();
        foreach ($products as $product) {
            $items[] = ['item' => $product['name']
                       ,'units' =>  (int)$product['cart_quantity']
                       ,'unitPrice' => round($product['price_wt'], 2)
                       ];
        }

        /** Order details data */
        $order_inputs = array();
        $order_inputs['orderId'] = $this->buildOrderId($cart->id);
        $order_inputs['type'] = $this->getOrderType();
        $order_inputs['amount'] = round((float)$cart->getOrderTotal(true, Cart::BOTH), 2);

        $currency = new Currency((int)$cart->id_currency);
        $order_inputs['currency'] = $currency->iso_code;

        $order_inputs['items'] = $items;

        /** Transaction details data */
        $inputs = array();
        $inputs['customer'] = $customer_inputs;
        $inputs['order'] = $order_inputs;
        $inputs['cardTransactionMode'] = $this->getCardTransactionMode();
        $inputs['invoiceEmail'] = "";
        $inputs['backUrl'] = $this->getBackUrl($cart);

        $inputs = $this->buildDataArray($inputs);

        $data = array();
        $data['inputs'] = $inputs;
        $data['action'] = $this->getPaymentFormActionUrl();

        return $data;
    }

    /** Method for adding siteId, apiKey and checksum to the data array
    *
    * @param array([key => value]) data: The data array.
    *
    * @return array([key => value]) - The data array containing added values
    *
    */
    public function buildDataArray($data)
    {
        $keys = self::getKeysInfo();
        if (!$keys) {
            return false;
        }
        $apiKey = $keys['privateKey'];
        $data['siteId'] = $keys['siteId'];

        $checksum = Twispay_Encoder::getBase64Checksum($data, $apiKey);
        $jsonRequest = Twispay_Encoder::getBase64JsonRequest($data);

        $data['checksum'] = $checksum;
        $data['jsonRequest'] = $jsonRequest;

        return $data;
    }

    /** Getter for order type
    *
    * @return string - Order type
    *
    */
    public function getOrderType()
    {
        return 'purchase';
    }

    /** Getter for transaction mode
    *
    * @return string - Transaction mode
    *
    */
    public function getCardTransactionMode()
    {
        return "authAndCapture";
    }

    /** Getter for action URL
    *
    * @return string - Action action URL
    *         boolean(false) - If the module keys are not defined
    *
    */
    public function getPaymentFormActionUrl()
    {
        $keys = self::getKeysInfo();
        if ($keys) {
            return $keys['formUrl'];
        }
        return false;
    }

    /** Getter for back URL
    *
    * @param object $cart - The cart object
    *
    * @return string - The resulted back URL
    *
    */
    public function getBackUrl($cart)
    {
        $id_customer = (int)$cart->id_customer;
        $secure_key = "";
        if ($id_customer) {
            $customer = new Customer($id_customer);
            if (Validate::isLoadedObject($customer)) {
                $secure_key = $customer->secure_key;
            }
        }
        return $this->context->link->getModuleLink(
            'twispay',
            'confirmation',
            array('cart_id' => $cart->id, 'secure_key' => $secure_key)
        );
    }

    /** Getter for customer ID
    *
    * @param object $customer id - The prestashop customer id
    *
    * @return string - The resulted customer id
    *
    */
    public function buildCustomerId($prefix,$customer_id)
    {
        return $prefix.'_ps_'.$customer_id.'_'.date('YmdHis');
    }

    /** Getter for order ID
    *
    * @param object $cart_id - The cart id
    *
    * @return string - The resulted order id
    *
    */
    public function buildOrderId($cart_id)
    {
        return $cart_id;
    }

    public function getPath()
    {
        return $this->_path;
    }
}
