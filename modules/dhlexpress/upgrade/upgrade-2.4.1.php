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
 * Upgrade to 2.4.1
 *
 * - Create and rename extra-charge
 *
 * @param $module Dhlexpress
 * @return bool
 * @throws PrestaShopException
 */
function upgrade_module_2_4_1($module)
{
    require_once(dirname(__FILE__).'/../classes/DhlExtracharge.php');

    $languages = Language::getLanguages(false);
    $ID800Id = DhlExtracharge::getIdByCode('HK');
    if (!$ID800Id) {
        $extracharges = array(
            array(
                'extracharge_code' => 'HK',
                'doc'              => 0,
                'dangerous'        => 1,
                'label'            => 'Dangerous Goods as per attached DGD',
                'names'            => array(
                    'fr' => 'ID 8000',
                    'en' => 'ID 8000',
                ),
                'descriptions'     => array(
                    'fr' => 'Marchandises dangereuses selon DGD',
                    'en' => 'Dangerous Goods as per attached DGD',
                ),
            ),
        );
        foreach ($extracharges as $extracharge) {
            $dhlExtracharge = new DhlExtracharge();
            $dhlExtracharge->hydrate($extracharge);
            $dhlExtracharge->active = 0;
            foreach ($languages as $language) {
                $name =
                    isset($extracharge['names'][$language['iso_code']]) ? $extracharge['names'][$language['iso_code']] :
                        $extracharge['names']['en'];
                $dhlExtracharge->name[(int) $language['id_lang']] = $name;
                $description = isset($extracharge['descriptions'][$language['iso_code']]) ?
                    $extracharge['descriptions'][$language['iso_code']] : $extracharge['descriptions']['en'];
                $dhlExtracharge->description[(int) $language['id_lang']] = $description;
            }
            if (!$dhlExtracharge->save()) {
                return false;
            }
        }
    }

    $fullIATAId = DhlExtracharge::getIdByCode('HE');
    $fullIATA = new DhlExtracharge((int) $fullIATAId);
    foreach ($languages as $language) {
        $fullIATA->name[(int) $language['id_lang']] = 'Full IATA';
    }
    if (!$fullIATA->save()) {
        return false;
    }

    $dbQuery = new DbQuery();
    $dbQuery->select('*');
    $dbQuery->from('dhl_plt');
    $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
    if (!$res) {
        $module->createPlt();
    }

    return true;
}
