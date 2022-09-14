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

include_once(_PS_MODULE_DIR_.'shipstation/classes/Carriers/ShpCarrierAbstractData.php');

class ShpCarrierBase extends ShpCarrierAbstractData
{
    /** @var Address */
    protected $pickupPoint;

    /**
     * ShpCarrierColissimo constructor.
     *
     * @param  Order  $order
     * @param  Carrier  $carrier
     */
    public function __construct(Order $order, Carrier $carrier)
    {
        parent::__construct($order, $carrier);

        $this->setPickupPoint();
    }

    /**
     * @return $this
     */
    protected function setPickupPoint()
    {
        $this->pickupPoint = new Address($this->order->id_address_delivery);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCompanyName()
    {
        return $this->pickupPoint->company;
    }

    /**
     * @inheritDoc
     */
    public function getAddress1()
    {
        return $this->pickupPoint->address1;
    }

    /**
     * @inheritDoc
     */
    public function getAddress2()
    {
        return $this->pickupPoint->address2;
    }

    /**
     * @inheritDoc
     */
    public function getAddress3()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCity()
    {
        return $this->pickupPoint->city;
    }

    /**
     * @inheritDoc
     */
    public function getZipCode()
    {
        return $this->pickupPoint->postcode;
    }

    /**
     * @inheritDoc
     */
    public function getCountry()
    {
        return $this->pickupPoint->country;
    }

    /**
     * @inheritDoc
     */
    public function getIsoCountry()
    {
        return (new Country($this->pickupPoint->id_country))->iso_code;
    }

    /**
     * @inheritDoc
     */
    public function getLocationId()
    {
        return $this->pickupPoint->other;
    }

    /**
     * @inheritDoc
     */
    public function getIsPickup()
    {
        $addressHasRelayId = $this->pickupPoint->other !== '';

        return $addressHasRelayId &&
            ($this->order->id_address_delivery !== $this->order->id_address_invoice);
    }
}
