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
 * @author    ShipStation
 * @copyright 2021 ShipStation
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

include_once(_PS_MODULE_DIR_.'shipstation/classes/Carriers/ShpCarrierDataInterface.php');

/**
 * Class ShpCarrierDataResponse
 */
final class ShpCarrierDataResponse
{
    /** @var string */
    const ORDER_ID_KEY = 'orderId';

    /** @var string */
    const ORDER_REFERENCE_KEY = 'orderReference';

    /** @var string */
    const CARRIER_ID_KEY = 'carrierId';

    /** @var string */
    const CARRIER_NAME_KEY = 'carrierName';

    /** @var string */
    const CARRIER_MODULE_NAME_KEY = 'carrierModule';

    /** @var string */
    const LOCATION_ID_KEY = 'locationId';

    /** @var string */
    const LOCATION_ADDRESS_KEY = 'locationAddress';

    /** @var string */
    const COMPANY_NAME_KEY = 'companyName';

    /** @var string */
    const ADDRESS_1_KEY = 'address1';

    /** @var string */
    const ADDRESS_2_KEY = 'address2';

    /** @var string */
    const ADDRESS_3_KEY = 'address3';

    /** @var string */
    const CITY_KEY = 'city';

    /** @var string */
    const ZIP_CODE_KEY = 'zipCode';

    /** @var string */
    const COUNTRY_KEY = 'country';

    /** @var string */
    const COUNTRY_ISO_KEY = 'countryIso';

    /** @var string */
    const IS_PICKUP_KEY = 'isPickup';

    /** @var ShpCarrierDataInterface */
    protected $carrierData;

    /**
     * ShpCarrierDataResponse constructor.
     *
     * @param  ShpCarrierDataInterface  $carrierData
     */
    public function __construct(ShpCarrierDataInterface $carrierData)
    {
        $this->carrierData = $carrierData;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::ORDER_ID_KEY            => $this->carrierData->getOrder()->id,
            self::ORDER_REFERENCE_KEY     => $this->carrierData->getOrder()->reference,
            self::CARRIER_ID_KEY          => $this->carrierData->getOrder()->id_carrier,
            self::CARRIER_NAME_KEY        => $this->carrierData->getCarrier()->name,
            self::CARRIER_MODULE_NAME_KEY => $this->carrierData->getCarrier()->external_module_name,
            self::LOCATION_ID_KEY         => $this->carrierData->getLocationId(),
            self::IS_PICKUP_KEY           => $this->carrierData->getIsPickup(),

            self::LOCATION_ADDRESS_KEY => [
                self::COMPANY_NAME_KEY => $this->carrierData->getCompanyName(),
                self::ADDRESS_1_KEY    => $this->carrierData->getAddress1(),
                self::ADDRESS_2_KEY    => $this->carrierData->getAddress2(),
                self::ADDRESS_3_KEY    => $this->carrierData->getAddress3(),
                self::CITY_KEY         => $this->carrierData->getCity(),
                self::ZIP_CODE_KEY     => $this->carrierData->getZipCode(),
                self::COUNTRY_KEY      => $this->carrierData->getCountry(),
                self::COUNTRY_ISO_KEY  => $this->carrierData->getIsoCountry(),
            ],
        ];
    }
}
