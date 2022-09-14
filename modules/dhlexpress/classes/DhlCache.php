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
 * Class DhlCache
 */
class DhlCache
{
    /**
     * @param array      $products
     * @param DhlAddress $senderAddress
     * @param Address    $customerAddress
     * @return string
     */
    public static function getCacheKey($products, $senderAddress, $customerAddress)
    {
        if (is_array($products) && !empty($products)) {
            $productString = implode(
                '|',
                array_map(
                    function ($entry) {
                        if (isset($entry['id_product']) && isset($entry['id_product_attribute'])) {
                            return $entry['id_product_attribute'].'_'.$entry['id_product'].'_'.$entry['quantity'];
                        } else {
                            return 0;
                        }
                    },
                    $products
                )
            );
        } else {
            $productString = '';
        }
        $senderAddressString = implode(
            '|',
            array(
                $senderAddress->zipcode,
                $senderAddress->city,
                $senderAddress->id_country,
                $senderAddress->id_state,
            )
        );
        $customerAddressString = implode(
            '|',
            array(
                $customerAddress->postcode,
                $customerAddress->city,
                $customerAddress->id_country,
                $customerAddress->id_state,
            )
        );

        return md5($productString.$senderAddressString.$customerAddressString);
    }

    /**
     * @param string $cacheKey
     * @param bool   $checkValidity
     * @return int|bool
     * @deprecated since 2.4.4
     */
    public static function isStored($cacheKey, $checkValidity = false)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('id_dhl_quote_cache, value');
        $dbQuery->from('dhl_quote_cache');
        $dbQuery->where('cache_key = "'.pSQL($cacheKey).'"');
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($dbQuery);
        if (!isset($row['id_dhl_quote_cache'])) {
            return 0;
        }
        if ($checkValidity && self::isCacheValueValid($row['value']) === false) {
            return false;
        }

        return (int) $row['id_dhl_quote_cache'];
    }

    /**
     * @param string $xmlString
     * @return bool
     * @deprecated since 2.4.4
     */
    public static function isCacheValueValid($xmlString)
    {
        try {
            $xmlObject = new SimpleXMLExtended($xmlString);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string           $cacheKey
     * @param DhlQuoteResponse $dhlQuoteResponse
     * @throws PrestaShopDatabaseException
     * @deprecated since 2.4.4
     */
    public static function store($cacheKey, DhlQuoteResponse $dhlQuoteResponse)
    {
        if (!self::isStored($cacheKey)) {
            Db::getInstance()->insert(
                'dhl_quote_cache',
                array(
                    'cache_key' => pSQL($cacheKey),
                    'value'     => pSQL($dhlQuoteResponse->responseXml->asXML(), true),
                )
            );
        } else {
            if (self::isCacheValueValid($dhlQuoteResponse->responseXml->asXML())) {
                Db::getInstance()->update(
                    'dhl_quote_cache',
                    array(
                        'value' => pSQL($dhlQuoteResponse->responseXml->asXML(), true),
                    ),
                    'cache_key = "'.pSQL($cacheKey).'"'
                );
            }
        }
    }

    /**
     * @param string $cacheKey
     * @return DhlQuoteResponse|false
     * @deprecated since 2.4.4
     */
    public static function retrieve($cacheKey)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('value');
        $dbQuery->from('dhl_quote_cache');
        $dbQuery->where('cache_key = "'.pSQL($cacheKey).'"');
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($dbQuery);
        try {
            $simpleXml = new SimpleXMLExtended($result);
        } catch (Exception $e) {
            return false;
        }

        return DhlQuoteResponse::buildFromResponse($simpleXml);
    }
}
