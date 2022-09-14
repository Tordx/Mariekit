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
 * Upgrade to 2.4.7
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_4_7($module)
{
    require_once(dirname(__FILE__).'/../classes/DhlCapital.php');
    require_once(dirname(__FILE__).'/../classes/DhlExtracharge.php');

    Db::getInstance()->execute("
        CREATE TABLE IF NOT EXISTS `ps_dhl_capital` (
            `id_dhl_capital` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `iso_country` VARCHAR(2) NOT NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
            `city` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
            `postcode` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
            `suburb` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
            `type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
            PRIMARY KEY (`id_dhl_capital`) USING BTREE,
            INDEX `iso_country` (`iso_country`) USING BTREE
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB
        AUTO_INCREMENT=195
        ;
    ");
    
    Db::getInstance()
               ->execute('ALTER TABLE `'._DB_PREFIX_.'dhl_extracharge` ADD COLUMN `extracharge_dg_code` VARCHAR(3) NULL AFTER `extracharge_code`');
    
    Configuration::updateValue('DHL_LABEL_IDENTIFIER', 'reference');

    $fd = fopen(dirname(__FILE__).'/../dhl_capitales.csv', 'r');
    fgetcsv($fd, null, ';');
    while ($line = fgetcsv($fd, null, ';')) {
        $capital = new DhlCapital();
        $capital->iso_country = pSQL($line[0]);
        $capital->city = pSQL($line[1]);
        $capital->postcode = pSQL($line[2]);
        $capital->suburb = pSQL($line[3]);
        $capital->type = (int) $line[4];
        if (!$capital->save()) {
            continue;
        }
    }
    fclose($fd);
    
    $extrachargesCodes = array('HH', 'HW', 'HM', 'HV', 'HD', 'HB', 'HE', 'HK', 'DD', 'IB', 'II');
    foreach ($extrachargesCodes as $code) {
        $idExtracharge = DhlExtracharge::getIdByCode($code);
        $dhlExtracharge = new DhlExtracharge((int) $idExtracharge);
        switch ($code) {
            case 'HH':
                $dgCode = 'E01';
                break;
            case 'HW':
                $dgCode = '970';
                break;
            case 'HM':
                $dgCode = '969';
                break;
            case 'HV':
                $dgCode = '967';
                break;  
            case 'HD':
                $dgCode = '966';
                break;    
            case 'HB':
                $dgCode = '965';
                break;     
            case 'HE':
                $dgCode = '910';
                break;         
            case 'HK':
                $dgCode = '700';
                break;         
            default:
                $dgCode = '';
                break;
        }
        $dhlExtracharge->extracharge_dg_code = pSQL($dgCode);
        if (!$dhlExtracharge->save()) {
            return false;
        }
    }

    return true;
}
