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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Necessary to access namespaced module classes in the main module file.
 */
require_once(__DIR__ . '/vendor/autoload.php');

use KlaviyoPs\Classes\HooksHandler;
use KlaviyoPs\Classes\BusinessLogicServices\ProductPayloadService;

/*
 * Necessary because there's no way to pass the fully namespaced class to WebserviceRequestCore. The class
 * is dynamically created using the first url segment after \api\. When registering resources a class name
 * can be added in the definition but this isn't used if it's a specific management class.
 *
 * Lines WebserviceRequest.php::607-608 in PrestaShop version 1.7.6.5
 * $specificObjectName = 'WebserviceSpecificManagement' . ucfirst(Tools::toCamelCase($this->urlSegment[0]));
 * if (!class_exists($specificObjectName)) { ...
 */
require_once(__DIR__ . '/classes/webservice/WebserviceSpecificManagementKlaviyo.php');

class KlaviyoPs extends Module
{
    /** @var string[] */
    const CONFIG_KEYS = [
        'KLAVIYO_PUBLIC_API',
        'KLAVIYO_PRIVATE_API',
        'KLAVIYO_IS_SYNCING_SUBSCRIBERS',
        'KLAVIYO_SUBSCRIBER_LIST',
        'KLAVIYO_ORDER_STATUS_MAP',
    ];

    const ADMIN_CONTROLLERS = array(
        array(
            'name' => 'Klaviyo',
            'visible' => true,
            'class_name' => 'AdminKlaviyoPsConfig',
            'parent_class_name' => 'CONFIGURE',
            'icon' => 'trending_up',
        ),
    );

    /** @var string[] Custom checkout module controller page names and corresponding input selectors. */
    const CUSTOM_CHECKOUTS_SELECTORS = array(
        'module-supercheckout-supercheckout' => 'input[name="supercheckout_email"]',
        'module-thecheckout-order' => 'input[type="email"]',
    );

    /**
     * Klaviyo constructor.
     */
    public function __construct()
    {
        $this->module_key = '8cbae1889fefef3589d3dcb95c0818aa';
        $this->name = 'klaviyops';
        $this->tab = 'advertising_marketing';
        $this->version = '1.2.9';
        $this->author = 'Klaviyo';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Klaviyo';
        $this->description = $this->l('Klaviyo module to integrate PrestaShop with Klaviyo.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('KLAVIYO')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * @return bool
     * @throws PrestaShopException
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        // Register hooks first to set up custom webservices so we can set permissions to custom resources.
        return parent::install() &&
            Configuration::updateValue('KLAVIYO', 'klaviyops') &&
            $this->registerControllersAndHooks() &&
            $this->setupWebservice() &&
            $this->installTabs();
    }

    /**
     * Install all Tabs.
     *
     * @return bool
     */
    public function installTabs()
    {
        foreach (static::ADMIN_CONTROLLERS as $adminTab) {
            if (false === $this->installTab($adminTab)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Install Tab.
     *
     * @param array $tabData
     * @return bool
     */
    public function installTab(array $tabData)
    {
        $tab = new Tab();
        $tab->id_parent = Tab::getIdFromClassName($tabData['parent_class_name']);
        $tab->name = array_fill_keys(array_values(Language::getIDs(false)), $tabData['name']);
        $tab->class_name = $tabData['class_name'];
        // Makes the module accessible in the Admin Controller, see ModuleAdminControllerCore constructor.
        $tab->module = $this->name;
        $tab->active = (bool) $tabData['visible'];
        $tab->icon = $tabData['icon'];

        return $tab->save();
    }

    /**
     * Turn on webservice, create token and set permissions.
     *
     * @return bool
     */
    private function setupWebservice()
    {
        if (
            !Configuration::updateValue('PS_WEBSERVICE', true) ||
            !$this->createWebserviceKey()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Auto-setup webservice key and permissions for API access.
     *
     * @return bool
     */
    private function createWebserviceKey()
    {
        $existingKey = Configuration::get('KLAVIYO_WEBSERVICE_KEY');

        // If we've already created a key, just pass.
        if (
            $existingKey &&
            WebserviceKey::keyExists($existingKey)
        ) {
            return true;
        }

        // Create and set the WebserviceKey object properties
        $webservice = new WebserviceKey();
        $key = Tools::passwdGen(32);
        $webservice->key = $key;
        $webservice->description = 'Klaviyo webservice key';

        // Save webservice key
        if (
            !$webservice->add() ||
            !Configuration::updateValue('KLAVIYO_WEBSERVICE_ID', $webservice->id) ||
            !Configuration::updateValue('KLAVIYO_WEBSERVICE_KEY', $webservice->key)
        ) {
            $this->_errors[] =
                $this->l('It was not possible to install the Klaviyo module: webservice key creation error.');
            return false;
        }

        // Set webservice key permissions
        if (!$this->setWebservicePermissionsForAccount($webservice->id, $this->getWebservicePermissions())) {
            $this->_errors[] =
                $this->l('It was not possible to install the Klaviyo module: webservice key permissions setup error.');
            return false;
        }
        return true;
    }

    /**
     * Get Webservice permissions for Klaviyo Webservice key.
     *
     * @return string[][]
     */
    private function getWebservicePermissions()
    {
        return array(
            'klaviyo' => array('GET', 'PUT', 'POST', 'DELETE', 'HEAD'),
        );
    }

    /**
     * Set permissions for resources on auto-created webservice token.
     *
     * HACK - Sadly, the built-in WebserviceKey::setPermissionForAccount method doesn't work in this flow. We register
     * our custom 'klaviyo' resource earlier in the installation process which should get picked up by the
     * addWebserviceResources hook in WebserviceRequest::getResources but it doesn't, our method to handle that hook
     * doesn't fire during installation (works fine later). So this method essentially does the same permission setting
     * but doesn't match against the available resources because our custom resource isn't available yet. We won't set
     * permissions if we can't register the hook because installation will fail earlier.
     *
     * @param $webserviceId
     * @param $permissionsToSet
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function setWebservicePermissionsForAccount($webserviceId, $permissionsToSet)
    {
        $success = true;
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'webservice_permission` WHERE `id_webservice_account` = ' . (int) $webserviceId;
        if (!Db::getInstance()->execute($sql)) {
            $success = false;
        }
        if (isset($permissionsToSet)) {
            $permissions = array();
            $methods = array('GET', 'PUT', 'POST', 'DELETE', 'HEAD');
            foreach ($permissionsToSet as $resource_name => $resource_methods) {
                foreach ($resource_methods as $method_name) {
                    if (in_array($method_name, $methods)) {
                        $permissions[] = array($method_name, $resource_name);
                    }
                }
            }
            $account = new WebserviceKey($webserviceId);
            if ($account->deleteAssociations() && $permissions) {
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'webservice_permission` (`id_webservice_permission` ,`resource` ,`method` ,`id_webservice_account`) VALUES ';
                foreach ($permissions as $permission) {
                    $sql .= '(NULL , \'' . pSQL($permission[1]) . '\', \'' . pSQL($permission[0]) . '\', ' . (int) $webserviceId . '), ';
                }
                $sql = rtrim($sql, ', ');
                if (!Db::getInstance()->execute($sql)) {
                    $success = false;
                }
            }
        }

        return $success;
    }

    /**
     * Register controllers and hooks.
     *
     * @return bool
     */
    public function registerControllersAndHooks()
    {
        return $this->registerHook('moduleRoutes') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('actionCustomerAccountUpdate') &&
            // Custom hook from ps_emailsubscription module (default newsletter subscribe form).
            $this->registerHook('actionNewsletterRegistrationAfter') &&
            $this->registerHook('addWebserviceResources') &&
            $this->registerHook('actionFrontControllerSetMedia');
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (
            !parent::uninstall() ||
            !Configuration::deleteByName('KLAVIYO') ||
            !$this->deleteKlaviyoConfigurationKeys() ||
            !$this->uninstallTabs() ||
            !$this->unregisterControllersAndHooks()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Delete module configuration keys.
     *
     * @return bool
     */
    public function deleteKlaviyoConfigurationKeys()
    {
        return count(array_filter(self::CONFIG_KEYS, 'Configuration::deleteByName')) == count(self::CONFIG_KEYS);
    }

    /**
     * Unregister controllers and hooks on module uninstall.
     *
     * @return bool
     */
    public function unregisterControllersAndHooks()
    {
        return $this->unregisterHook('moduleRoutes') &&
            $this->unregisterHook('actionCustomerAccountAdd') &&
            $this->unregisterHook('actionCustomerAccountUpdate') &&
            // Custom hook from ps_emailsubscription module (default newsletter subscribe form).
            $this->unregisterHook('actionNewsletterRegistrationAfter') &&
            $this->unregisterHook('addWebserviceResources') &&
            $this->unregisterHook('actionFrontControllerSetMedia');
    }

    /**
     * Uninstall all Tabs.
     *
     * @return bool
     */
    public function uninstallTabs()
    {
        foreach (static::ADMIN_CONTROLLERS as $adminTab) {
            if (false === $this->uninstallTab($adminTab)) {
                $this->_errors[] = 'Failed to uninstall all tabs.';
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall Tab.
     *
     * @param array $tabData
     * @return bool
     */
    public function uninstallTab(array $tabData)
    {
        $tabId = Tab::getIdFromClassName($tabData['class_name']);
        $tab = new Tab($tabId);

        if (false === (bool) $tab->delete()) {
            return false;
        }

        return true;
    }

    /**
     * Validate form and handle requests for the displayForm action.
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKlaviyoPsConfig'));
    }

    /**
     * Configuration::get() defaults to config values at higher shop scopes if key is not set. This pulls in config
     * values for our form that aren't actually set. Basically undoing this to return null if the specific shop scope
     * does not have a value set.
     *
     * @param $configKey
     * @return bool|string|null
     */
    public function getConfigurationValueOrNull($configKey) {
        // If Multi-store is not active, getContext() returns Shop::CONTEXT_SHOP.
        if (!Shop::isFeatureActive()) {
            return Configuration::get($configKey);
        }

        if ($this->context->shop->getContext() === Shop::CONTEXT_SHOP && Configuration::hasKey($configKey, null, null, $this->context->shop->id)) {
            return Configuration::get($configKey, null, null, $this->context->shop->id);
        } elseif ($this->context->shop->getContext() === Shop::CONTEXT_GROUP && Configuration::hasKey($configKey, null, Shop::getContextShopGroupID(true))) {
            return Configuration::get($configKey, null, Shop::getContextShopGroupID(true));
        } elseif ($this->context->shop->getContext() === Shop::CONTEXT_ALL) {
            return Configuration::get($configKey);
        }

        return null;
    }

    /**
     * Setup custom routes.
     *
     * @return array
     */
    public function hookModuleRoutes()
    {
        return [
            'module-klaviyo-reclaim' => [
                'rule' => 'klaviyo/reclaim/cart',
                'keywords' => [],
                'controller' => 'reclaim',
                'params' => [
                    'fc' => 'module',
                    'module' => 'klaviyops'
                ]
            ],
            'module-klaviyo-build' => [
                'rule' => 'klaviyo/reclaim/build-reclaim',
                'keywords' => [],
                'controller' => 'buildReclaim',
                'params' => [
                    'fc' => 'module',
                    'module' => 'klaviyops'
                ]
            ],
            'module-klaviyo-add-to-cart' => [
                'rule' => 'klaviyo/events/add-to-cart',
                'keywords' => [],
                'controller' => 'addToCart',
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name
                ]
            ]
        ];
    }

    /**
     * Handle actionCustomerAccountAdd hook.
     * @param array $params
     */
    public function hookActionCustomerAccountAdd(array $params)
    {
        $hooksHandler = new HooksHandler($this);
        $hooksHandler->handleActionCustomerAccount($params);
    }

    /**
     * Handle actionCustomerAccountUpdate hook.
     * @param array $params
     */
    public function hookActionCustomerAccountUpdate(array $params)
    {
        $hooksHandler = new HooksHandler($this);
        $hooksHandler->handleActionCustomerAccount($params);
    }

    /**
     * Handle actionNewsletterRegistrationAfter hook from ps_emailsubscription module.
     *
     * @param array $params
     */
    public function hookActionNewsletterRegistrationAfter(array $params)
    {
        $hooksHandler = new HooksHandler($this);
        $hooksHandler->handleActionNewsletterSubscription($params);
    }

    /**
     * Handle addWebserviceResources hook. This method needs to return the webservice definition
     * for the new endpoint in order to register it properly in WebserviceRequestCore::getResources().
     *
     * @param array $params
     * @return array[]
     */
    public function hookAddWebserviceResources(array $params)
    {
        $hooksHandler = new HooksHandler($this);
        return $hooksHandler->handleAddWebserviceResources($params);
    }

    /**
     * Handle actionFrontControllerSetMedia hook. Fires on all Front Office pages
     * and calls methods to inject javascript files and dependent data.
     *
     * @param $params
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        if ($this->getConfigurationValueOrNull('KLAVIYO_PUBLIC_API')) {
            $this->setupKlaviyoAnalytics();
            $this->setupProductEvents();
            $this->setupStartedCheckout();
        }
    }

    /**
     * Register klaviyo.js and identify javascript code along with customer data if
     * public API key is set.
     */
    private function setupKlaviyoAnalytics()
    {
        if (!$this->isCheckoutPage()) {
            $this->addIdentifyData();
            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-analytics',
                'https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=' . $this->getConfigurationValueOrNull('KLAVIYO_PUBLIC_API'),
                array(
                    'server' => 'remote',
                    'priority' => 450,
                    'attributes' => 'async'
                )
            );
            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-identify',
                'modules/' . $this->name . '/js/klaviyops-identify.js',
                array(
                    'priority' => 451,
                )
            );
        }
    }

    /**
     * Define global js variables of customer for identifying with _learnq.
     */
    private function addIdentifyData()
    {
        $customer = $this->context->customer;
        Media::addJsDef(
            array(
                'klCustomer' => array(
                    'email' => $customer->email,
                    'firstName' => $customer->firstname,
                    'lastName' => $customer->lastname,
                )
            )
        );
    }

    /**
     * Setup Viewed Product and Add to Cart events if on Product page.
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function setupProductEvents()
    {
        if ($this->context->controller->php_self == 'product') {
            $this->setupViewedProduct();
            $this->setupAddToCart();
        }
    }

    /**
     * Add viewed product data and register javascript file.
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function setupViewedProduct()
    {
        $this->addViewedProductData();
        $this->context->controller->registerJavascript(
            'module-' . $this->name . '-viewed-product',
            'modules/' . $this->name . '/js/klaviyops-viewed-product.js',
            array(
                'priority' => 460,
            )
        );
    }

    /**
     * Define javascript global for Viewed Product event.
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function addViewedProductData()
    {
        $lang_id = $this->context->language->id;
        $shop_id = $this->context->shop->id;
        $product = new Product(Tools::getValue('id_product'), $full=false, $id_lang=$lang_id, $id_shop=$shop_id);
        $product_id = $product->id;

        // Build categories array
        $productCategories = array();
        foreach (Product::getProductCategoriesFull($product_id, $lang_id) as $category) {
            $productCategories[] = $category['name'];
        };

        // Get product URL and allow context to handle language.
        $link = new Link;
        $productLink = $link->getProductLink($product);

        Media::addJsDef(
            array(
                'klProduct' => array(
                    'ProductName' => $product->name,
                    'ProductID' => $product_id,
                    'SKU' => $product->reference,
                    'Tags' => ProductPayloadService::getProductTagsArray($product_id, $lang_id),
                    'Price' => number_format($product->price, 2),
                    'SpecialPrice' => number_format(Product::getPriceStatic($product_id), 2),
                    'Categories' => $productCategories,
                    'Image' => ProductPayloadService::buildProductImageUrls($product),
                    'Link' => $productLink,
                    'ShopID' => $shop_id,
                    'LangID' => $lang_id
                )
            )
        );
    }

    /**
     * Register Added to Cart javascript.
     */
    private function setupAddToCart()
    {
        $this->context->controller->registerJavascript(
            'module-' . $this->name . '-add-to-cart',
            'modules/' . $this->name . '/js/klaviyops-add-to-cart.js',
            array(
                'priority' => 465,
            )
        );
    }

    /**
     * Add started checkout data and register javascript file.
     */
    private function setupStartedCheckout()
    {
        if ($this->shouldInsertStartedCheckoutJavascript()) {
            $this->addStartedCheckoutData();
            $this->context->controller->registerJavascript(
                'module-' . $this->name . '-started-checkout',
                'modules/' . $this->name . '/js/klaviyops-started-checkout.js',
                array(
                    'priority' => 470,
                )
            );
        }
    }

    /**
     * Add javascript definition to DOM for Started Checkout events.
     */
    private function addStartedCheckoutData()
    {
        Media::addJsDef(
            array(
                'klStartedCheckout' => array(
                    'cartId' => isset(Context::getContext()->cart->id) ? Context::getContext()->cart->id : false,
                    'email' => $this->context->customer->email,
                    'token' => Tools::getToken(),
                    'baseUrl' => Tools::getShopDomainSsl(true),
                    'emailInputSelector' => $this->getCheckoutEmailInputSelector(),
                )
            )
        );
    }

    /**
     * Returns custom checkout email input field selector if not default checkout.
     *
     * @return string
     */
    private function getCheckoutEmailInputSelector()
    {
        if ($this->isCustomCheckoutPage()) {
            return self::CUSTOM_CHECKOUTS_SELECTORS[$this->context->controller->page_name];
        }

        // Default checkout page selector.
        return '[type="email"]';
    }

    /**
     * Confirm if checkout session is on the Personal Information Step to capture email address and only send
     * event once.
     *
     * @param $sessionData
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    private function isCheckoutPersonalInfoStep($sessionData)
    {
        // If a customer hasn't been to the checkout page yet, this value in the DB will be NULL. Treat as first step.
        if (!isset($sessionData)) {
            return true;
        }
        $personalInfoStep = $sessionData['checkout-personal-information-step'];
        return $personalInfoStep['step_is_reachable'] && !$personalInfoStep['step_is_complete'];
    }

    /**
     * Confirm if checkout session is on the Addresses step and the customer is Logged in.
     *
     * @param $sessionData
     * @return bool
     */
    private function isLoggedInAddressesStep($sessionData)
    {
        $addressesStep = $sessionData['checkout-addresses-step'];
        return $this->context->customer->isLogged() && $addressesStep['step_is_reachable'] && !$addressesStep['step_is_complete'];
    }

    /**
     * Confirm page default checkout or one of known custom checkouts for injecting Started Checkout code.
     *
     * @return bool
     */
    private function isCheckoutPage()
    {
        return $this->isDefaultCheckoutPage() || $this->isCustomCheckoutPage();
    }

    /**
     * Confirm page is Order front controller and checkout page.
     *
     * @return bool
     */
    private function isDefaultCheckoutPage()
    {
        return $this->context->controller->php_self == 'order' && $this->context->controller->page_name == 'checkout';
    }

    /**
     * Confirm page is a known custom checkout we want to support.
     *
     * @return bool
     */
    private function isCustomCheckoutPage()
    {
        return (
            property_exists($this->context->controller, 'page_name')
            && in_array($this->context->controller->page_name, array_keys(self::CUSTOM_CHECKOUTS_SELECTORS))
        );
    }

    /**
     * Confirm this is a valid step in the checkout process for injecting Started Checkout code.
     *
     * @return bool
     */
    private function isCorrectCheckoutStep()
    {
        $cartId = $this->context->cart->id;
        if (!$cartId) {
            return false;
        }

        $select = 'SELECT checkout_session_data ';
        $from = 'FROM ' . _DB_PREFIX_ . 'cart ';
        $where = 'WHERE id_cart = ' . (int) $cartId;

        $sql = $select . $from . $where;
        try {
            $result = Db::getInstance()->ExecuteS($sql);
        } catch (PrestaShopDatabaseException $e) {
            return false;
        }

        if (!$result || !array_key_exists('checkout_session_data', $result[0])) {
            return false;
        }

        $sessionData = json_decode($result[0]['checkout_session_data'], true);

        return $this->isCheckoutPersonalInfoStep($sessionData) || $this->isLoggedInAddressesStep($sessionData);
    }

    /**
     * Determine if we should inject the Started Checkout javascript page type
     * and the given point in the checkout process.
     *
     * @return bool
     */
    private function shouldInsertStartedCheckoutJavascript()
    {
        return $this->isCheckoutPage() && $this->isCorrectCheckoutStep();
    }
}
