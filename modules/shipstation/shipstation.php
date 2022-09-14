<?php
/**
 * 2007-2021 PrestaShop
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
 * @author    ShipStation
 * @copyright 2021 ShipStation
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class ShipStation
 */
class ShipStation extends Module
{
    /** @var array|string[] */
    private $messages;

    /**
     * ShipStation module constructor.
     */
    public function __construct()
    {
        $this->tab = 'shipping_logistics';
        $this->name = 'shipstation';
        $this->author = 'ShipStation';
        $this->version = '1.0.5';
        $this->bootstrap = true;
        $this->module_key = 'f0b919b30912bc4aae4fc116ab152dc7';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.6.1', 'max' => _PS_VERSION_];

        parent::__construct();

        $this->setMessages();

        $this->displayName = $this->l('ShipStation', $this->name);
        $this->description = $this->l('Click on the ShipStation icon in the sidebar to get started.', $this->name);
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?', $this->name);

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided', $this->name);
        }
    }

    /**
     * @return $this
     */
    protected function setMessages()
    {
        $this->messages = [
            'displayName'     => $this->l('ShipStation', $this->name),
            'logoText'        => $this->l('The fastest, easiest way to get products to your customers.', $this->name),
            'startTrial'      => $this->l('START YOUR FREE TRIAL!', $this->name),
            'haveAccount'     => $this->l('Already have an account?', $this->name),
            'login'           => $this->l('Login', $this->name),
            'now'             => $this->l('now.', $this->name),
            'importOrders'    => $this->l(
                'Import all your PrestaShop orders (from everywhere else you sell, too) automatically. Never copy-paste an address again – EVER!',
                $this->name
            ),
            'createLabels'    => $this->l('Create shipping labels for your preferred carrier.', $this->name),
            'customizeViews'  => $this->l(
                'Customize your views and assign specific roles to different users o make ShipStation match your workflow.',
                $this->name
            ),
            'createRules'     => $this->l(
                'Create automation rules and watch ShipStation apply the best shipping settings, package types, and so much more for you.',
                $this->name
            ),
            'activateAccount' => $this->l(
                'With our seamless integration to your PrestaShop account, you’ll go from 0 to shipping in no time. Activate your free account now to start shipping efficiently!',
                $this->name
            ),
        ];

        return $this;
    }

    /**
     * @param $identifier string
     *
     * @return string
     */
    public function getMessage($identifier)
    {
        if (!isset($this->messages[$identifier])) {
            // Explicitly forbid someone to retrieve any non-defined message
            throw new PrestaShopException('Message identifier not found.');
        }

        return $this->messages[$identifier];
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        include_once(_PS_MODULE_DIR_.'shipstation/src/Install/Installer.php');

        $installer = new Installer();

        return $installer->install($this->context, $this);
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        include_once(_PS_MODULE_DIR_.'shipstation/src/Install/Installer.php');

        $installer = new Installer();


        return $installer->uninstall();
    }

    /**
     * @return false|string
     */
    public function getContent()
    {
        $this->smarty->assign(
            [
                'logoText'        => $this->getMessage('logoText'),
                'startTrial'      => $this->getMessage('startTrial'),
                'haveAccount'     => $this->getMessage('haveAccount'),
                'login'           => $this->getMessage('login'),
                'now'             => $this->getMessage('now'),
                'importOrders'    => $this->getMessage('importOrders'),
                'createLabels'    => $this->getMessage('createLabels'),
                'customizeViews'  => $this->getMessage('customizeViews'),
                'createRules'     => $this->getMessage('createRules'),
                'activateAccount' => $this->getMessage('activateAccount'),
            ]
        );

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }
}
