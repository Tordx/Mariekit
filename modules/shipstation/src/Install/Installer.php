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

/**
 * Class Installer
 */
class Installer
{
    /** @var Context */
    protected $context;

    /** @var Module */
    private $module;

    /**
     * @param  Context  $context  *
     *
     * @return bool
     */
    public function install(Context $context, Module $module)
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $this->context = $context;
        $this->module = $module;

        Tools::clearAllCache();

        return $this->installTab(
            'IMPROVE',
            'AdminShipStation'
        );
    }

    /**
     * @param $parent_class
     * @param $class_name
     *
     * @return bool
     */
    public function installTab($parent_class, $class_name)
    {
        $tab = new Tab();

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name [(int)$lang['id_lang']] = 'ShipStation';
        }

        $tab->class_name = $class_name;
        $tab->id_parent = (int)Tab::getIdFromClassName($parent_class);
        $tab->module = $this->module->name;
        $tab->active = 1;
        $tab->icon = 'settings';

        return $tab->add();
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        if (!Configuration::deleteByName('landing_page_url')) {
            return false;
        }

        return true;
    }
}
