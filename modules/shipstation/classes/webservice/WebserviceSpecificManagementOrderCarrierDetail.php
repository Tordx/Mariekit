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

include_once(_PS_MODULE_DIR_.'shipstation/classes/Carriers/ShpCarrierBase.php');

/**
 * Class WebserviceSpecificManagementOrderCarrierDetail
 */
class WebserviceSpecificManagementOrderCarrierDetail implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilder */
    protected $objOutput;

    /** @var WebserviceRequestCore */
    protected $wsObject;

    /** @var string */
    protected $output;

    /** @var string */
    protected $urlSegment;

    /** @var int */
    protected $orderId;

    /** @var string */
    protected $schema;

    /**
     * Association map for `external_module_name` column in table `ps_carrier` to handler class.
     * List of currently supported carriers.
     *
     * @var ShpCarrierDataInterface[]
     */
    protected $supportedCarriers = [
        'colissimo'    => ShpCarrierBase::class,
        'chronopost'   => ShpCarrierBase::class,
        'mondialrelay' => ShpCarrierBase::class,
    ];

    /**
     * @return string
     */
    public function getUrlSegment()
    {
        return $this->urlSegment;
    }

    /**
     * @param $segments
     *
     * @return $this
     */
    public function setUrlSegment($segments)
    {
        $this->urlSegment = $segments;

        return $this;
    }

    /**
     * @return WebserviceRequestCore
     */
    public function getWsObject()
    {
        return $this->wsObject;
    }

    /**
     * @param  WebserviceRequestCore  $obj
     *
     * @return $this
     */
    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    /**
     * @return string
     */
    public function getObjectOutput()
    {
        return $this->objOutput->getObjectRender()->overrideContent($this->output);
    }

    /**
     * This must be return a string with specific values as WebserviceRequest expects.
     *
     * @return string
     */

    public function getContent()
    {
        return $this->output;
    }

    /**
     * @param  WebserviceOutputBuilderCore  $obj
     *
     * @return WebserviceSpecificManagementInterface
     */

    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;

        return $this;
    }

    /**
     * @return bool
     * @throws WebserviceException
     */
    public function manage()
    {
        $this->orderId = (int)$this->wsObject->urlSegment[1];

        if (!$this->orderId) {
            return false;
        }

        if ($this->getSchema() === 'xml') {
            $this->buildXmlResponse();

            return true;
        }

        $this->buildJsonResponse();

        return true;
    }

    /**
     * @return void
     * @throws WebserviceException
     */
    private function buildJsonResponse()
    {
        $this->output = json_encode(['data' => $this->buildObject()]);
    }

    /**
     * @return void
     * @throws WebserviceException
     */
    private function buildXmlResponse()
    {
        include_once(_PS_MODULE_DIR_.'shipstation/classes/Support/XMLSerializer.php');

        $this->output = XMLSerializer::generateValidXmlFromArray($this->buildObject(), 'data');
    }

    /**
     * @return array
     * @throws WebserviceException
     */
    private function buildObject()
    {
        include_once(_PS_MODULE_DIR_.'shipstation/classes/Carriers/ShpCarrierDataResponse.php');

        $order = new Order($this->orderId);
        $this->checkOrderExisting($order);

        $carrier = new Carrier($order->id_carrier);
        $carrierModule = $carrier->external_module_name;

        $this->checkCarrierSupport($carrierModule, $carrier->name);

        /** @var ShpCarrierDataInterface $carrierData */
        $carrierData = new $this->supportedCarriers[$carrierModule]($order, $carrier);

        return (new ShpCarrierDataResponse($carrierData))->toArray();
    }

    /**
     * Return 404 if order not exists
     *
     * @param  Order  $order
     *
     * @throws WebserviceException
     */
    private function checkOrderExisting(Order $order)
    {
        if (!$order->id) {
            throw new WebserviceException('Information for specified order not found.', [404, 404]);
        }
    }

    /**
     * Return 422 if order carrier not present in $this->supportedCarriers
     *
     * @param  string  $carrierModuleName
     * @param  string  $carrierName
     *
     * @throws WebserviceException
     */
    private function checkCarrierSupport($carrierModuleName, $carrierName)
    {
        if (!array_key_exists($carrierModuleName, $this->supportedCarriers)) {
            throw new WebserviceException('Carrier '.$carrierName.' not supported.', [404, 404]);
        }
    }

    /**
     * @return string 'xml' | 'json'
     */
    private function getSchema()
    {
        if (array_key_exists('output_format', $this->wsObject->urlFragments)) {
            return Tools::strtolower($this->wsObject->urlFragments['output_format']);
        }

        return 'xml';
    }
}
