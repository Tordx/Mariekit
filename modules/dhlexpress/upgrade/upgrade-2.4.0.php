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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2021 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to 2.4.0
 *
 * - Upate cache value field type
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_4_0($module)
{
    $query = Db::getInstance()->execute(
        '
            CREATE TABLE IF NOT EXISTS `'. _DB_PREFIX_ .'dhl_plt` (
                `id_plt` INT(11) NOT NULL AUTO_INCREMENT,
                `country_code` VARCHAR(3) NOT NULL,
                `amount` INT(11) NOT NULL,
                `inbound` TINYINT(1) NOT NULL,
                `outbound` TINYINT(1) NOT NULL,
                `currency` VARCHAR(5) NOT NULL,
                `conversion_rate` FLOAT NOT NULL,
                PRIMARY KEY (`id_plt`)
            )
        '
    );

    return $query;
}
