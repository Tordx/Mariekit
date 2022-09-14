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
 * Upgrade to 2.3.3
 *
 * - Create table for quotation cache
 *
 * @param $module Dhlexpress
 * @return bool
 */
function upgrade_module_2_3_3($module)
{
    Db::getInstance()->execute(
        '
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dhl_quote_cache` (
                `id_dhl_quote_cache` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `cache_key` VARCHAR(50) NOT NULL,
                `value` TEXT NOT NULL,
                PRIMARY KEY (`id_dhl_quote_cache`),
                INDEX `cache_key` (`cache_key`)
            )
        '
    );

    return true;
}
