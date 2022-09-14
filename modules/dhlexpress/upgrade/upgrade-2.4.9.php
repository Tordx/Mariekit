<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author     PrestaShop SA <contact@prestashop.com>
 * @copyright  2007-2021 PrestaShop SA
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 2.4.9
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_4_9($module)
{
    Configuration::updateValue('DHL_ACCOUNT_ID_TEST', 'v62_flN5Lb50Fg');
    Configuration::updateValue('DHL_ACCOUNT_PASSWORD_TEST', 'O2onOI5Gms');
    Configuration::updateValue('DHL_ACCOUNT_ID_PRODUCTION', 'v62_SVUxv9EGyg');
    Configuration::updateValue('DHL_ACCOUNT_PASSWORD_PRODUCTION', 'tPDOsuPnWL');
    
    $dbQuery = new DbQuery();
    $dbQuery->select('ds.id_dhl_service');
    $dbQuery->from('dhl_service', 'ds');
    $dbQuery->where('ds.product_content_code = "ESI"');
    $id = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($dbQuery);
    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'dhl_service_lang` SET `name`= "DHL Economy - Suisse, Norvège, UK" WHERE id_lang = '.Language::getIdByIso("fr").' AND id_dhl_service ='. (int) $id);
    Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'dhl_service_lang` SET `name`= "DHL Economy - Switzerland, Norway, UK" WHERE id_lang != '.Language::getIdByIso("fr").' AND id_dhl_service =' . (int) $id);

    return true;
}
