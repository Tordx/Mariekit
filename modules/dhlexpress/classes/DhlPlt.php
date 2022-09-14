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
 * Class DhlPlt
 */
class DhlPlt extends ObjectModel
{
    /** @var int $id_plt */
    public $id_plt;

    /** @var string $country_code */
    public $country_code;

    public $amount;

    public $inbound;

    public $outbound;

    public $currency;

    public $conversion_rate;

    /** @var array $definition */
    public static $definition = array(
        'table'     => 'dhl_plt',
        'primary'   => 'id_plt',
        'multilang' => false,
        'fields'    => array(
            'country_code' => array(
                'type'     => self::TYPE_STRING,
                'required' => false,
                'size'     => 3,
            ),
            'amount'   => array(
                'type'     => self::TYPE_INT,
                'required' => false,
                'size'     => 11,
            ),
            'inbound' => array(
                'type'     => self::TYPE_INT,
                'required' => false,
                'size'     => 1,
            ),
            'outbound'   => array(
                'type'     => self::TYPE_INT,
                'required' => false,
                'size'     => 1,
            ),
            'currency' => array(
                'type'     => self::TYPE_STRING,
                'required' => false,
                'size'     => 5,
            ),
            'conversion_rate'   => array(
                'type'     => self::TYPE_FLOAT,
                'required' => false,
            ),
        ),
    );

    /**
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public static function getList()
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('dt.*');
        $dbQuery->from(self::$definition['table'], 'dt');
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($dbQuery);
        $return = array();
        foreach ($results as $result) {
            $return[$result['country_code']] = (int) $result['id_plt'];
        }

        return $return;
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function getByCountryCode($countryCode)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('dt.id_plt')
                ->from(self::$definition['table'], 'dt')
                ->where('country_code = "'.pSQL($countryCode).'"');
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($dbQuery);

        return new self((int) $id);
    }
}
