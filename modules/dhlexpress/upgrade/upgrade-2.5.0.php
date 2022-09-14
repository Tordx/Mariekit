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
 * Upgrade to 2.5.0
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_5_0($module)
{
    require_once(dirname(__FILE__).'/../classes/DhlPlt.php');
    require_once(dirname(__FILE__).'/../classes/logger/AbstractDhlHandler.php');
    require_once(dirname(__FILE__).'/../classes/logger/DhlNullHandler.php');
    require_once(dirname(__FILE__).'/../classes/logger/DhlFileHandler.php');
    require_once(dirname(__FILE__).'/../classes/logger/DhlLogger.php');

    if (Configuration::get('DHL_ENABLE_LOG')) {
        $version = str_replace('.', '_', $module->version);
        $hash = Tools::encrypt(_PS_MODULE_DIR_.$module->name.'/logs/');
        $file = dirname(__FILE__).'/../logs/dhlexpress_'.$hash.'.log';
        $logger = new DhlLogger('DHL_'.$version.'_Label', new DhlFileHandler($file));
    } else {
        $logger = new DhlLogger('', new DhlNullHandler());
    }

    $logger->info('Start upgrading v2.5.0');

    $shopIds = Shop::getShops();
    foreach ($shopIds as $id) {
        Configuration::updateValue('DHL_ACCOUNT_ID_TEST', 'v62_KBsvWxqJDW', false, null, $id);
        Configuration::updateValue('DHL_ACCOUNT_PASSWORD_TEST', '2ShcGBJigc', false, null, $id);
        Configuration::updateValue('DHL_ACCOUNT_ID_PRODUCTION', 'v62_DIMk5KyUhb', false, null, $id);
        Configuration::updateValue('DHL_ACCOUNT_PASSWORD_PRODUCTION', 'y2xv9gtmUQ', false, null, $id);
    }

    $fd = fopen(dirname(__FILE__).'/../dhl_plt.csv', 'r');
    while ($line = fgetcsv($fd, null, ';')) {
        try {
            $plt = DhlPlt::getByCountryCode($line[1]);
            $plt->amount = $line[2];
            $plt->inbound = $line[3];
            $plt->outbound = $line[4];
            $plt->save();
        } catch (Exception $e) {
            $logger->error($e->getMessage());
            continue;
        }
    }

    return true;
}
