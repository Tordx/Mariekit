<?php
/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'astropay` (
    `id_cart` int(11) NOT NULL,
    `id_order` int(11) NOT NULL,
    `secure_key` varchar(32) NOT NULL,
    `callback_key` varchar(15) NOT NULL,
    `redirect_key` varchar(15) NOT NULL,
    `merchant_deposit_id` varchar(15) DEFAULT NULL,
    `deposit_external_id` varchar(128) DEFAULT NULL,
    `deposit_status` varchar(15) DEFAULT NULL,
    `merchant_cashout_id` varchar(15) DEFAULT NULL,
    `cashout_external_id` varchar(128) DEFAULT NULL,
    `refund_status` varchar(15) DEFAULT NULL,
    PRIMARY KEY  (`id_cart`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
