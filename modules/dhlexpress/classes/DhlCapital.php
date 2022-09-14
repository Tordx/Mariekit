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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class DhlCapital
 */
class DhlCapital extends ObjectModel
{
    const CAPITAL_CITY = 1;
    const CAPITAL_CITY_SUBURB = 2;
    const CAPITAL_CITY_POSTCODE = 3;

    /** @var int $id_dhl_capital */
    public $id_dhl_capital;

    /** @var string $iso_country */
    public $iso_country;

    /** @var string $city */
    public $city;

    /** @var string $postcode */
    public $postcode;

    /** @var string $suburb */
    public $suburb;

    /** @var int $type 1 = City ; 2 = City + Suburb ; 3 = City + Postcode */
    public $type;

    /** @var array $definition */
    public static $definition = array(
        'table' => 'dhl_capital',
        'primary' => 'id_dhl_capital',
        'fields' => array(
            'iso_country' => array('type' => self::TYPE_STRING, 'size' => 2, 'required' => true),
            'city' => array('type' => self::TYPE_STRING, 'size' => 50, 'required' => true),
            'postcode' => array('type' => self::TYPE_STRING, 'size' => 20, 'required' => false),
            'suburb' => array('type' => self::TYPE_STRING, 'size' => 50, 'required' => false),
            'type' => array('type' => self::TYPE_INT, 'size' => 1, 'required' => true),
        ),
    );

    /**
     * @param string $isoCountry
     * @return array
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getReceiverByIsoCountry($isoCountry)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_dhl_capital, city, postcode, suburb, type')
                ->from('dhl_capital')
                ->where(sprintf('iso_country = "%s"', pSQL($isoCountry)));

        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($dbQuery);
        if (!$row) {
            
            return false;
        }
        $dhlCapital = new self((int) $row['id_dhl_capital']);
        $receiverArray = array(
            'CountryCode' => $isoCountry,
        );
        if ($dhlCapital->type === self::CAPITAL_CITY) {
            return $receiverArray;
        } elseif ($dhlCapital->type === self::CAPITAL_CITY_SUBURB) {
            $receiverArray['City'] = $dhlCapital->city;
            $receiverArray['Suburb'] = $dhlCapital->suburb;
        } else {
            $receiverArray['Postalcode'] = $dhlCapital->postcode;
            $receiverArray['City'] = $dhlCapital->city;
        }

        return $receiverArray;
    }
}
