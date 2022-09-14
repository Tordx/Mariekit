<?php
/**
 * Klaviyo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact extensions@klaviyo.com
 *
 * @author    Klaviyo
 * @copyright Klaviyo
 * @license   commercial
 */

namespace KlaviyoPs\Classes\BusinessLogicServices;

use Configuration;
use DateTime;
use DateTimeZone;

use ObjectModelCore;

class PayloadServiceInterface
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * Build payload for object either to return in API response or send via webhook.
     *
     * @param ObjectModelCore $objectModel
     * @param $id_shop
     * @param int $shopId
     * @return array
     * @throws \BadMethodCallException Method must be implemented by child classes.
     */
    public static function buildPayload(ObjectModelCore $objectModel, $id_shop = null)
    {
        throw new \BadMethodCallException('buildPayload() method must be implemented by child of ' . self::class);
    }

    /**
     * Remove sensitive keys from Objects so we don't return this information in payloads. Need to convert
     * to an array using encode/decode otherwise we will get lots of additional properties we don't want.
     *
     * @param ObjectModelCore $toClean
     * @param array $sensitiveKeys
     * @return array
     */
    protected static function removeSensitiveKeys(ObjectModelCore $toClean, array $sensitiveKeys)
    {
        return array_diff_key(self::objectToArray($toClean), array_flip($sensitiveKeys));
    }

    /**
     * Convert ObjectModelCore and children to array.
     *
     * @param ObjectModelCore $obj
     * @return mixed
     */
    protected static function objectToArray(ObjectModelCore $obj)
    {
        return json_decode(json_encode($obj), true);
    }

    /**
     * Convert date and time from local timezone to UTC.
     *
     * @param $date
     * @return string
     */
    protected static function convertDateStringToUTC($date)
    {
        $shopTimezone = new DateTimeZone(Configuration::get('PS_TIMEZONE'));
        $datetime = DateTime::createFromFormat(self::DATE_TIME_FORMAT, $date, $shopTimezone);

        return $datetime->setTimezone(new DateTimeZone('UTC'))->format(self::DATE_TIME_FORMAT);
    }
}
