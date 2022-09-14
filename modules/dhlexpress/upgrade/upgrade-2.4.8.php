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
 * Upgrade to 2.4.8
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_4_8($module)
{   
    require_once(dirname(__FILE__).'/../classes/DhlCapital.php');
    require_once(dirname(__FILE__).'/../classes/logger/loader.php');
    
    $columnExists = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'dhl_address` LIKE "eori"');
    if (empty($columnExists)) {
        Db::getInstance()
               ->execute('ALTER TABLE `'._DB_PREFIX_.'dhl_address` ADD COLUMN `eori` VARCHAR(35) NULL AFTER `phone`');
    }
    $columnExists = Db::getInstance()->executeS('SHOW COLUMNS FROM `'._DB_PREFIX_.'dhl_address` LIKE "vat_gb"');
    if (empty($columnExists)) {
        Db::getInstance()
               ->execute('ALTER TABLE `'._DB_PREFIX_.'dhl_address` ADD COLUMN `vat_gb` VARCHAR(35) NULL AFTER `eori`');
    }
    Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dhl_capital` (
                `id_dhl_capital` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `iso_country` VARCHAR(2) NOT NULL DEFAULT "",
                `city` VARCHAR(50) NOT NULL DEFAULT "",
                `postcode` VARCHAR(20) NULL DEFAULT NULL,
                `suburb` VARCHAR(50) NULL DEFAULT NULL,
                `type` TINYINT(1) UNSIGNED NOT NULL DEFAULT "0",
                PRIMARY KEY (`id_dhl_capital`),
                INDEX `iso_country` (`iso_country`)
            )
    '); 
    $rows = Db::getInstance()->getValue('
	SELECT COUNT(*)
	FROM `'._DB_PREFIX_.'dhl_capital`');
    
    if((int) $rows == 0){
        $fd = fopen(dirname(__FILE__).'/../dhl_capitales.csv', 'r');
        fgetcsv($fd, null, ';');
        if (Configuration::get('DHL_ENABLE_LOG')) {
            $version = str_replace('.', '_', $module->version);
            $hash = Tools::encrypt(_PS_MODULE_DIR_.$module->name.'/logs/');
            $file = dirname(__FILE__).'/../logs/dhlexpress_'.$hash.'.log';
            $logger = new DhlLogger('DHL_'.$version.'_SaveDhlCapital', new DhlFileHandler($file));
        } else {
            $logger = new DhlLogger('', new DhlNullHandler());
        }
        while ($line = fgetcsv($fd, null, ';')) {
            $capital = new DhlCapital();
            $capital->iso_country = pSQL($line[0]);
            $capital->city = pSQL($line[1]);
            $capital->postcode = pSQL($line[2]);
            $capital->suburb = pSQL($line[3]);
            $capital->type = pSQL($line[4]);
            try {
                $capital->save();
            } catch (Exception $e) {
                $logger->error($e);
                continue;
           }
        }
        fclose($fd);
    }
    
    return true;
}
