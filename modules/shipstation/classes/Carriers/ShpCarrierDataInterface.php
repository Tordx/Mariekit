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

/**
 * Interface ShpCarrierDataInterface
 */
interface ShpCarrierDataInterface
{
    /**
     * ShpCarrierDataInterface constructor.
     *
     * @param  Order  $order
     * @param  Carrier  $carrier
     */
    public function __construct(Order $order, Carrier $carrier);

    /**
     * @return int
     */
    public function getLocationId();

    /**
     * @return string
     */
    public function getCompanyName();

    /**
     * @return string
     */
    public function getAddress1();

    /**
     * @return string
     */
    public function getAddress2();

    /**
     * @return string
     */
    public function getAddress3();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getZipCode();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getIsoCountry();

    /**
     * @return Order
     */
    public function getOrder();

    /**
     * @return Carrier
     */
    public function getCarrier();

    /**
     * @return bool
     */
    public function getIsPickup();
}
