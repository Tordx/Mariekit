<?php
/**
 * Klaviyo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact extensions@klaviyo.com
 *
 * @author    Klaviyo
 * @copyright Klaviyo
 * @license   commercial
 */

use Klaviyo\Exception\KlaviyoException;
use KlaviyoPs\Classes\KlaviyoApiWrapper;
use KlaviyoPs\Classes\BusinessLogicServices\OrderPayloadService;

class AdminKlaviyoPsConfigController extends ModuleAdminController
{
    const PUBLIC_KEY_VALIDATION_REGEX = '/^[a-zA-Z0-9]{6}$/';
    const PRIVATE_KEY_VALIDATION_REGEX = '/^(pk_)[a-zA-Z0-9]{34}$/';

    /** @var string Minimum version compatible for subscribing profiles using default newsletter form. */
    const PS_EMAILSUBSCRIPTION_MIN_VERSION = '2.6.0';

    public function __construct()
    {
        // Bootstrap set to true to utilize default admin controller styling.
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        $allValues = Tools::getAllValues();

        if (Tools::isSubmit('submit' . $this->module->name)) {
            $this->validateAndSaveConfig('validateConfigValues', 'saveFormKlaviyoValues', $allValues);
        } elseif (Tools::isSubmit('submit' . $this->module->name . 'OrderStatus')) {
            $this->validateAndSaveConfig('validateOrderStatusMapValues', 'saveFormOrderStatusMapValues', $allValues);
        }

        if (!$this->module->getConfigurationValueOrNull('KLAVIYO_PUBLIC_API')) {
            $text_question = $this->l('Don\'t have a Klaviyo account?');
            $text_create_account = $this->l('Create your account here.');

            $this->informations[] = $text_question . ' <a target="_blank" href="https://www.klaviyo.com/signup/prestashop">' . $text_create_account . '</a>';
        }

        // Prompt to submit order status mapping if default has not been accepted on initial setup.
        if (!$this->module->getConfigurationValueOrNull('KLAVIYO_ORDER_STATUS_MAP')) {
            $this->warnings[] = $this->l('Please complete the order status mapping form or accept the default values.');
        }

        $this->content .= $this->renderForm();
        $this->content .= $this->renderOrderStatusMapForm();
        parent::initContent();
    }

    /**
     * @return string
     */
    public function renderForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Klaviyo Public API Key'),
                    'placeholder' => 'Aa1234',
                    'id' => 'kl_public_key',
                    'name' => 'KLAVIYO_PUBLIC_API',
                    'value' => Configuration::get('KLAVIYO_PUBLIC_API'),
                    'size' => 6,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Klaviyo Private API Key'),
                    'placeholder' => 'pk_111222333444555666777888999',
                    'id' => 'kl_private_key',
                    'name' => 'KLAVIYO_PRIVATE_API',
                    'value' => Configuration::get('KLAVIYO_PRIVATE_API'),
                    'size' => 50,
                    'required' => true,
                    'desc' => '<a target="_blank" href="https://help.klaviyo.com/hc/en-us/articles/115005062267-Manage-Your-Account-s-API-Keys">Need help finding your API keys?</a>'
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Sync subscribers to a list in Klaviyo'),
                    'name' => 'KLAVIYO_IS_SYNCING_SUBSCRIBERS',
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
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        // Fetch lists for Account.
        $lists = [];
        if ($this->module->getConfigurationValueOrNull('KLAVIYO_PRIVATE_API')) {
            try {
                $api = new KlaviyoApiWrapper();
                $lists = $api->getLists();
            } catch (KlaviyoException $e) {
                $this->errors[] = $this->l(
                    'There was an error accessing lists for account: ' . Configuration::get('KLAVIYO_PUBLIC_API')
                );
            }
        }

        // Ensure we have an array and lists in it.
        if (is_array($lists) && !empty($lists) && $this->module->getConfigurationValueOrNull('KLAVIYO_IS_SYNCING_SUBSCRIBERS')) {
            // Add default value and "placeholder" option. Defaults to null in db if saved.
            $list_arr = [
                array(
                    'id_option' => '',
                    'name' => $this->l('Select a list'),
                    'value' => '',
                )
            ];
            foreach ($lists as $list) {
                $list_arr[] = array(
                    'id_option' => $list['list_id'],
                    'name' => $list['list_name'],
                    'value' => $list['list_id'],
                    'default' => array(
                        'value' => $list['list_id'],
                        'label' => 'list'
                    ),
                );
            }

            // TODO: We might want to allow someone to reset this value to null.
            // TODO: Provide option whether to subscribe use members or subscribe endpoint.
            // Build form to select Klaviyo list.
            $fieldsForm[]['form'] = [
                'legend' => [
                    'title' => $this->l('Lists'),
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => 'Klaviyo Subscriber List',
                        'name' => 'KLAVIYO_SUBSCRIBER_LIST',
                        'required' => false,
                        'options' => [
                            'query' => $list_arr,
                            'id' => 'id_option',
                            'name' => 'name'
                        ],
                        'desc' => $this->l('Klaviyo will adhere to the double opt-in settings for the selected list.'),
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right'
                ]
            ];

            // Check version of ps_emailsubscription module and display notice if incompatible for list subscription.
            if (!$this->isPsEmailsubscriptionCompatible()) {
                $fieldsForm[1]['form']['warning'] = $this->l(sprintf(
                    'If you want to subscribe profiles to a Klaviyo list using the 
                    PrestaShop \'Newsletter Subscription\' module, please make sure the
                    module is enabled and at least version %s or higher.',
                    self::PS_EMAILSUBSCRIPTION_MIN_VERSION
                ));
            }
        }

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this->module;
        $helper->name_controller = $this->module->name;
        $helper->token = Tools::getAdminTokenLite($this->controller_name);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->module->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->module->name.'&save'.$this->module->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current values
        $helper->fields_value['KLAVIYO_PUBLIC_API'] = $this->module->getConfigurationValueOrNull('KLAVIYO_PUBLIC_API');
        $helper->fields_value['KLAVIYO_PRIVATE_API'] = $this->module->getConfigurationValueOrNull('KLAVIYO_PRIVATE_API');
        $helper->fields_value['KLAVIYO_SUBSCRIBER_LIST'] = $this->module->getConfigurationValueOrNull('KLAVIYO_SUBSCRIBER_LIST');
        $helper->fields_value['KLAVIYO_IS_SYNCING_SUBSCRIBERS'] = $this->module->getConfigurationValueOrNull('KLAVIYO_IS_SYNCING_SUBSCRIBERS');

        $helper->fields_value['KLAVIYO_LANGUAGE'] = Configuration::get('KLAVIYO_LANGUAGE');
        return $helper->generateForm($fieldsForm);
    }

    /**
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function renderOrderStatusMapForm()
    {
        $orderStates = OrderState::getOrderStates($this->context->language->id);
        $fieldsForm[]['form'] = array(
            'legend' => array('title' => $this->l('Order status mapping')),
            'description' => $this->l(
                'Select one or more order statuses to map to Klaviyo order related events. For example, Klaviyo can 
                record a Fulfilled Order when an order has the status of "Shipped".'
            ),
            'input'  => array(
                array(
                    'type'     => 'select',
                    'multiple' => true,
                    'label'    => $this->l('Status(es) for Placed Order event'),
                    'name'     => 'klaviyops-statuses-placed',
                    'id'       => 'id_order_state_placed',
                    'size'     => count($orderStates),
                    'options'  => array(
                        'query' => $orderStates,
                        'id'    => 'id_order_state',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'     => 'select',
                    'multiple' => true,
                    'label'    => $this->l('Status(es) for Refunded Order event'),
                    'name'     => 'klaviyops-statuses-refunded',
                    'id'       => 'id_order_state_refunded',
                    'size'     => count($orderStates),
                    'options'  => array(
                        'query' => $orderStates,
                        'id'    => 'id_order_state',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'     => 'select',
                    'multiple' => true,
                    'label'    => $this->l('Status(es) for Canceled Order event'),
                    'name'     => 'klaviyops-statuses-canceled',
                    'id'       => 'id_order_state_canceled',
                    'size'     => count($orderStates),
                    'options'  => array(
                        'query' => $orderStates,
                        'id'    => 'id_order_state',
                        'name'  => 'name',
                    ),
                ),
                array(
                    'type'     => 'select',
                    'multiple' => true,
                    'label'    => $this->l('Status(es) for Fulfilled Order event'),
                    'name'     => 'klaviyops-statuses-fulfilled',
                    'id'       => 'id_order_state_fulfilled',
                    'size'     => count($orderStates),
                    'options'  => array(
                        'query' => $orderStates,
                        'id'    => 'id_order_state',
                        'name'  => 'name',
                    ),
                ),
            ),
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        );
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this->module;
        $helper->name_controller = $this->module->name;
        $helper->token = Tools::getAdminTokenLite($this->controller_name);
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;

        // Language
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = $this->context->language->id;

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->module->name . 'OrderStatus';
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->module->name.'&save'.$this->module->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];
        // Load current values
        $helper->tpl_vars = array(
            'fields_value' => $this->getInitialOrderStatusMapValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Validate configuration form values and save them if there are no errors during validation.
     *
     * @param string $validationMethod
     * @param string $saveMethod
     * @param array $values
     */
    private function validateAndSaveConfig($validationMethod, $saveMethod, $values)
    {
        $config_form_errors = call_user_func(array($this, $validationMethod), $values);
        if ($config_form_errors) {
            $this->errors[] = $this->l(implode(' ', $config_form_errors));
        } else {
            call_user_func(array($this, $saveMethod), $values);
            $this->confirmations[] = $this->l('Settings updated');
        }
    }

    /**
     * Validate Klaviyo config form values.
     *
     * @param array $values Values in submitted form.
     *
     * @return array Error messages if config values aren't valid.
     */
    private function validateConfigValues($values)
    {
        $public_key = trim($values['KLAVIYO_PUBLIC_API']);
        $private_key = trim($values['KLAVIYO_PRIVATE_API']);
        $err = [];

        if ($public_key && !preg_match(self::PUBLIC_KEY_VALIDATION_REGEX, $public_key)) {
            array_push($err, 'Invalid Public API Key.');
        }

        if ($private_key && !preg_match(self::PRIVATE_KEY_VALIDATION_REGEX, $private_key)) {
            array_push($err, 'Invalid Private API Key.');
        }

        if (!$public_key || !$private_key) {
            array_push($err, 'Public and Private API keys are required.');
        }

        return $err;
    }
    
    /**
     * Save Klaviyo-specific configuration values.
     *
     * @param array $values Values in submitted form.
     */
    private function saveFormKlaviyoValues($values)
    {
        foreach ($values as $key => $value) {
            if (in_array($key, KlaviyoPs::CONFIG_KEYS)) {
                Configuration::updateValue($key, trim($value));
            }
        }
        // Ensure KLAVIYO_SUBSCRIBER_LIST is null if KLAVIYO_IS_SYNCING_SUBSCRIBERS is false.
        if (!$values['KLAVIYO_IS_SYNCING_SUBSCRIBERS'] && array_key_exists('KLAVIYO_SUBSCRIBER_LIST', $values)) {
            Configuration::updateValue('KLAVIYO_SUBSCRIBER_LIST', null);
        }
    }

    /**
     * Check if an order status was selected in more than one event type. This will extract only input fields from all
     * the submission keys/values. Then merge all of the values (selected status IDs) into an indexed array to check
     * for duplicates.
     *
     * @param $values
     *
     * @return array Error messages if config values aren't valid.
     */
    private function validateOrderStatusMapValues($values)
    {
        $statusMapValues = array_intersect_key($values, OrderPayloadService::ORDER_STATUS_MAP_DEFAULT);
        $allSelectedOrderStatuses = call_user_func_array('array_merge', array_values($statusMapValues));

        $err = array();
        if (count(array_unique($allSelectedOrderStatuses)) < count($allSelectedOrderStatuses)) {
            $err[] = 'Cannot select the same order status in multiple event maps.';
        }

        return $err;
    }

    /**
     * Save order status map values to configuration table. Combine these inputs into a single associative array.
     *
     * @param $values
     */
    private function saveFormOrderStatusMapValues($values)
    {
        $statusMap = array();
        foreach ($values as $key => $value) {
            if (in_array($key, array_keys(OrderPayloadService::ORDER_STATUS_MAP_DEFAULT))) {
                $statusMap[$key] = $value;
            }
        }

        Configuration::updateValue('KLAVIYO_ORDER_STATUS_MAP', json_encode($statusMap));
    }

    /**
     * Get existing order status map values and format for form. The multi-select option requires an array where
     * the keys correspond to the input name and has '[]' appended e.g.
     *     array(
     *         'klaviyops-statuses-placed[]' => array("3", "4", "10")
     *     )
     *
     * Default values will be populated in the form if mapping hasn't been set for the selected store scope.
     *
     * @return array
     */
    private function getInitialOrderStatusMapValues()
    {
        $jsonMap = $this->module->getConfigurationValueOrNull('KLAVIYO_ORDER_STATUS_MAP');
        $statusMap = json_decode($jsonMap, true);

        $initialValues = array();
        foreach (OrderPayloadService::ORDER_STATUS_MAP_DEFAULT as $field => $value) {
            $initialValues[$field . '[]'] = isset($statusMap[$field]) ? $statusMap[$field] : $value;
        }

        return $initialValues;
    }

    /**
     * Klaviyo uses the 'actionNewsletterRegistrationAfter' hook to subscribe customers to the selected list when a
     * user adds their email address to the PrestaShop Newsletter Subscription module form. This hook was only added
     * in version 2.6.0 so check to ensure compatibility and module is enabled.
     *
     * @return bool
     */
    private function isPsEmailsubscriptionCompatible()
    {
        $emailsubs_module = Module::getInstanceByName('ps_emailsubscription');
        if ($emailsubs_module && $emailsubs_module->isEnabledForShopContext()) {
            return version_compare($emailsubs_module->version, self::PS_EMAILSUBSCRIPTION_MIN_VERSION, '>=');
        }
        return false;
    }
}
