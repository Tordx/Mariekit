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
 * Class AdminDhlLabelController
 */

class AdminDhlLabelController extends ModuleAdminController
{
    const PLT_NOT_AVAILABLE = -1;
    const PLT_AVAILABLE_NOT_ELIGIBLE = 0;
    const PLT_AVAILABLE = 1;

    private $data;

    /**
     * @var DhlLogger $logger
     */
    private $logger;

    /** @var array */
    private $labelFormat = array(
        'pdfa4'  => array(
            'LabelImageFormat' => 'PDF',
            'LabelTemplate'    => '8X4_A4_PDF',
        ),
        'pdf64'  => array(
            'LabelImageFormat' => 'PDF',
            'LabelTemplate'    => '6X4_PDF',
        ),
        'zpl264' => array(
            'LabelImageFormat' => 'ZPL2',
            'LabelTemplate'    => '6X4_thermal',
        ),
        'epl264' => array(
            'LabelImageFormat' => 'EPL2',
            'LabelTemplate'    => '6X4_thermal',
        ),
    );

    /**
     * AdminDhlLabelController constructor.
     * @throws PrestaShopException
     */
    public function __construct()
    {
        require_once(dirname(__FILE__).'/../../api/loader.php');
        require_once(dirname(__FILE__).'/../../classes/DhlTools.php');
        require_once(dirname(__FILE__).'/../../classes/DhlOrder.php');
        require_once(dirname(__FILE__).'/../../classes/DhlAddress.php');
        require_once(dirname(__FILE__).'/../../classes/DhlPackage.php');
        require_once(dirname(__FILE__).'/../../classes/DhlCommercialInvoice.php');
        require_once(dirname(__FILE__).'/../../classes/HTMLTemplateDhlInvoice.php');
        require_once(dirname(__FILE__).'/../../classes/DhlLabel.php');
        require_once(dirname(__FILE__).'/../../classes/DhlService.php');
        require_once(dirname(__FILE__).'/../../classes/DhlExtracharge.php');
        require_once(dirname(__FILE__).'/../../classes/DhlError.php');
        require_once(dirname(__FILE__).'/../../classes/logger/loader.php');
        require_once(dirname(__FILE__).'/../../classes/DhlPDFGenerator.php');
        
        $this->bootstrap = true;
        $this->context = Context::getContext();
        parent::__construct();

        if (Configuration::get('DHL_ENABLE_LOG')) {
            $version = str_replace('.', '_', $this->module->version);
            $hash = Tools::encrypt(_PS_MODULE_DIR_.$this->module->name.'/logs/');
            $file = dirname(__FILE__).'/../../logs/dhlexpress_'.$hash.'.log';
            $this->logger = new DhlLogger('DHL_'.$version.'_Label', new DhlFileHandler($file));
        } else {
            $this->logger = new DhlLogger('', new DhlNullHandler());
        }
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addjQueryPlugin('typewatch');
        $this->addjQueryPlugin('fancybox');
    }

    /**
     * @return bool|ObjectModel
     * @throws Exception
     * @throws SmartyException
     */
    public function postProcess()
    {
        $action = Tools::getValue('action');
        if (!$action) {
            // Create a label not related to a PrestaShop order
            $this->displayFreeLabelForm();
        }
        $idDhlLabel = (int) Tools::getValue('id_dhl_label');
        if ($action == 'create') {
            // Create a label related to a PrestaShop order
            $idOrder = (int) Tools::getValue('id_order');
            $dhlOrder = DhlOrder::getByIdOrder($idOrder);
            if (!Validate::isLoadedObject($dhlOrder)) {
                return parent::postProcess();
            }
            $this->displayLabelForm($dhlOrder);
        } elseif ($action == 'createreturn') {
            // Create a return label related to a PrestaShop order
            $dhlLabel = new DhlLabel((int) $idDhlLabel);
            if (!Validate::isLoadedObject($dhlLabel)) {
                return parent::postProcess();
            }
            $dhlOrder = new DhlOrder((int) $dhlLabel->id_dhl_order);
            if (!Validate::isLoadedObject($dhlOrder)) {
                return parent::postProcess();
            }
            $this->context->smarty->assign('return_label', $idDhlLabel);
            $dhlReturnLabel = $dhlLabel->getDhlReturnLabel();
            if (false !== $dhlReturnLabel) {
                if (!Validate::isLoadedObject($dhlReturnLabel)) {
                    // Return label already exists but object not valid
                    return parent::postProcess();
                } else {
                    // Return label already exists
                    $this->displayExistingLabel($dhlReturnLabel);
                }
            } else {
                $this->displayLabelForm($dhlOrder);
            }
        } elseif ('downloadlabel' == $action) {
            // Download an existing label
            $this->downloadLabel($idDhlLabel);
        } elseif ($action == 'downloadFreeLabel') {
            $this->downloadFreeLabel();
        }
        if ($action == 'uploadPdfInvoice') {
            try {
                $this->context->controller->confirmations[] = $this->postProcessUploadPdfInvoice();
            } catch (Exception $e) {
                $this->context->smarty->assign('import_failed', true);
                $this->context->smarty->assign('import_error', $e->getMessage());
            }
        }
        return parent::postProcess();
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function assignVars()
    {
        $defaultSenderAddrDelivery = (int) Configuration::get('DHL_DEFAULT_SENDER_ADDRESS');
        $senderAddresses = DhlAddress::getAddressList();
        $packageTypes = DhlPackage::getPackageList();
        $defaultPackageType = (int) Configuration::get('DHL_DEFAULT_PACKAGE_TYPE');
        $defaultShipmentContent = Configuration::get('DHL_DEFAULT_SHIPMENT_CONTENT');
        $type_designation = Configuration::get('TYPE_DESIGNATION_UN_XXXX');
        $dimensionUnit = DhlTools::getDimensionUnit();
        $weightUnit = DhlTools::getWeightUnit();
        $extracharges = DhlExtracharge::getExtrachargesList($this->context->language->id);
        $updateDhlAddrLink = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
        $updatePackageLink = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
        $this->context->smarty->assign(
            array(
                'link'                     => $this->context->link,
                'dhl_img_path'             => $this->module->getPathUri().'views/img/',
                'currentIndex'             => $this->context->link->getAdminLink('AdminDhlLabel'),
                'default_package_type'     => $defaultPackageType,
                'default_sender_address'   => $defaultSenderAddrDelivery,
                'default_shipment_content' => $defaultShipmentContent,
                'type_designation'         => $type_designation,
                'dhl_extracharges'         => $extracharges,
                'dimension_unit'           => $dimensionUnit,
                'extracharge_insurance'    => DhlExtracharge::getIdByCode('II'),
                'extracharge_excepted'     => DhlExtracharge::getIdByCode('HH'),
                'extracharge_liability'    => DhlExtracharge::getIdByCode('IB'),
                'extracharge_dangerous'    => DhlExtracharge::getIdByCode('HE'),
                'extracharge_DTP'          => DhlExtracharge::getIdByCode('DD'),
                'package_types'            => $packageTypes,
                'sender_addresses'         => $senderAddresses,
                'update_dhl_addr_link'     => $updateDhlAddrLink,
                'update_package_link'      => $updatePackageLink,
                'weight_unit'              => $weightUnit,
            )
        );
    }

    /**
     * @param $dhlLabel
     * @throws Exception
     * @throws SmartyException
     */
    public function displayExistingLabel($dhlLabel)
    {
        $this->assignVars();
        $dhlService = new DhlService((int) $dhlLabel->id_dhl_service);
        $this->context->smarty->assign(
            array(
                'errors'           => false,
                'id_dhl_label'     => $dhlLabel->id,
                'alreadyGenerated' => true,
                'freeLabel'        => false,
                'labelDetails'     => array(
                    'GlobalProductCode' => $dhlService->global_product_code,
                    'ProductShortName'  => $dhlService->global_product_name,
                    'AirwayBillNumber'  => $dhlLabel->awb_number,
                    'LabelImage'        => array(
                        'OutputFormat' => '',
                        'OutputImage'  => $dhlLabel->label_string,
                    ),
                ),
                'link'             => $this->context->link,
                'dhl_img_path'     => $this->module->getPathUri().'views/img/',
            )
        );
        $header = $this->createTemplate('../_partials/dhl-header.tpl')->fetch();
        $content = $this->createTemplate('./_partials/dhl-label-result.tpl')->fetch();
        $this->content = $header.$content;
    }

    /**
     * @param DhlOrder $dhlOrder
     * @throws Exception
     * @throws SmartyException
     */
    public function displayLabelForm($dhlOrder)
    {
        $action = Tools::getValue('action');
        $this->assignVars();
        $idOrder = (int) $dhlOrder->id_order;
        $order = new Order((int) $idOrder);
        $cart = new Cart((int) $order->id_cart);
        $customerAddrDelivery = new Address((int) $order->id_address_delivery);
        $updateAddrLink =
            $this->context->link->getAdminLink('AdminAddresses').'&updateaddress&id_address='.$customerAddrDelivery->id;
        $orderCurency = new Currency((int) $order->id_currency);
        $dhlOrder = DhlOrder::getByIdOrder((int) $order->id);
        $orderIdentifier = Configuration::get('DHL_LABEL_IDENTIFIER');
        $dhlService = new DhlService((int) $dhlOrder->id_dhl_service);
        $doc = $dhlService->doc;
        $customer_country_iso = DhlTools::getIsoCountryById((int) $customerAddrDelivery->id_country);
        //$senderAddresses = DhlAddress::getAddressList();
        $helperUpload = new HelperUploader('eg_pdf_invoice');
        $helperUpload->setId(null);
        $helperUpload->setName('eg_pdf_invoice');
        $helperUpload->setUrl(null);
        $helperUpload->setMultiple(false);
        $helperUpload->setUseAjax(false);
        $helperUpload->setMaxFiles(null);
        $helperUpload->setFiles(array( 0 => array('type' => HelperUploader::TYPE_FILE,
                'size' => null,
                'delete_url' => null,
                'download_url' => null,)));
        $helperUpload->useAjax();
        $defaultSenderAddrDelivery = (int) Configuration::get('DHL_DEFAULT_SENDER_ADDRESS');
        $senderAddr = new DhlAddress((int) $defaultSenderAddrDelivery);

        $this->context->smarty->assign(
            array(
                'declared_value_with_taxes'    => $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
                'declared_value_without_taxes' => $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING),
                'currency_iso'                 => $orderCurency->iso_code,
                'customer_address'             => $customerAddrDelivery,
                'customer_country_iso'         => $customer_country_iso,
                'id_order'                     => $idOrder,
                'dhl_sending_doc'              => $doc,
                'update_addr_link'             => $updateAddrLink,
                'shipper_id'                   => $orderIdentifier == 'reference' ? $order->reference : $order->id,
                'service_plt'                  => $this->enablePltService($customer_country_iso, DhlTools::getIsoCountryById((int) $senderAddr->id_country), $customerAddrDelivery->postcode),
                'input_upload'                 => $helperUpload->render(),
                'action'                       => $action,
            )
        );

        $this->assingParamsInvoice($order);
        $this->content = $this->createTemplate('dhl_label.tpl')->fetch();
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function displayFreeLabelForm()
    {
        $action = Tools::getValue('action');
        $this->assignVars();
        $countries = Country::getCountries($this->context->language->id);
        $defaultCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $this->context->smarty->assign(
            array(
                'countries'       => $countries,
                'currency_iso'    => $defaultCurrency->iso_code,
                'dhl_sending_doc' => (int) Configuration::get('DHL_SENDING_DOC'),
                'default_country' => Country::getByIso('FR'),
                'shipper_id'      => '',
                'action'          => $action,
            )
        );
        $this->content = $this->createTemplate('dhl_free_label.tpl')->fetch();
    }

    /**
     * @param int $idDhlLabel
     */
    public function downloadLabel($idDhlLabel)
    {
        $dhlLabel = new DhlLabel((int) $idDhlLabel);
        if (!Validate::isLoadedObject($dhlLabel)) {
            die($this->module->l('Invalid Label.', 'AdminDhlLabel'));
        }
        $labelType = $dhlLabel->label_format;
        $decoded = base64_decode($dhlLabel->label_string);
        $file = sys_get_temp_dir();
        if ($file && Tools::substr($file, -1) != DIRECTORY_SEPARATOR) {
            $file .= DIRECTORY_SEPARATOR;
        }
        if (in_array($labelType, array('pdf', 'zpl', 'epl'))) {
            $file .= $dhlLabel->awb_number.'.'.$labelType;
        } else {
            die($this->module->l('Invalid Label.', 'AdminDhlLabel'));
        }
        file_put_contents($file, $decoded);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
            unlink($file);
            exit;
        }
    }

    /**
     *
     */
    public function downloadFreeLabel()
    {
        $filetype = Tools::getValue('dhl_free_label_filetype');
        if ($filetype == 'ZPL2') {
            $labelType = 'zpl';
        } elseif ($filetype == 'EPL2') {
            $labelType = 'epl';
        } else {
            $labelType = 'pdf';
        }
        $decoded = base64_decode(Tools::getValue('dhl_free_label_base64'));
        $file = sys_get_temp_dir();
        if ($file && Tools::substr($file, -1) != DIRECTORY_SEPARATOR) {
            $file .= DIRECTORY_SEPARATOR;
        }
        $awbNumber = Tools::getValue('dhl_free_label_awbnumber');
        if (in_array($labelType, array('pdf', 'zpl', 'epl'))) {
            $file .= $awbNumber.'.'.$labelType;
        } else {
            die($this->module->l('Invalid Label.', 'AdminDhlLabel'));
        }
        file_put_contents($file, $decoded);
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($file));
            readfile($file);
            unlink($file);
            exit;
        }
    }

    /**
     * @param bool $doc
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getChosenExtracharges($doc = false)
    {
        $list = array();
        $extracharges = DhlExtracharge::getExtrachargesList($this->context->language->id);
        foreach ($extracharges as $extracharge) {
            $idDhlExtracharge = (int) $extracharge['id_dhl_extracharge'];
            if (Tools::isSubmit('extracharge_'.$idDhlExtracharge) &&
                1 == (int) Tools::getValue('extracharge_'.$idDhlExtracharge)
            ) {
                if (($doc && $extracharge['doc']) || (!$doc && !$extracharge['doc'])) {
                    $dhlExtracharge = new DhlExtracharge($idDhlExtracharge);
                    $list[$dhlExtracharge->extracharge_code] = $dhlExtracharge->label;
                }
            }
        }

        return $list;
    }

    /**
     * @param array $packagesDimension
     * @return array
     */
    public function getProductsParams($packagesDimension)
    {
        $productsParam = array();
        if ($packagesDimension) {
            foreach ($packagesDimension as $packageDimension) {
                $values = explode('x', $packageDimension);
                if (is_array($values) && 5 == count($values)) {
                    $productsParam[] = array(
                        'PieceID' => (int) $values[0],
                        'Weight'  => (float) $values[1],
                        'Width'   => (int) $values[3],
                        'Height'  => (int) $values[2],
                        'Depth'   => (int) $values[4],
                    );
                }
            }
        }

        return $productsParam;
    }

    /**
     * @param float $orderTotalWeight
     * @return array
     */
    public function getBulkProductsParams($orderTotalWeight)
    {
        $productsParams = array();
        $idDhlPackage = Tools::getValue('dhl_package_type');
        if (Tools::getValue('dhl_use_order_weight')) {
            $weight = $orderTotalWeight;
        } else {
            $weight = Tools::getValue('dhl_package_weight_'.(int) $idDhlPackage);
        }
        $productsParams[] = array(
            'PieceID' => (int) $idDhlPackage,
            'Weight'  => (float) $weight,
            'Width'   => (int) Tools::getValue('dhl_package_width_'.(int) $idDhlPackage),
            'Height'  => (int) Tools::getValue('dhl_package_length_'.(int) $idDhlPackage),
            'Depth'   => (int) Tools::getValue('dhl_package_depth_'.(int) $idDhlPackage),
        );

        return $productsParams;
    }

    /**
     * @param array $productsParam
     * @return int
     */
    public function getTotalWeight($productsParam)
    {
        $weight = 0;
        foreach ($productsParam as $productParam) {
            $weight += $productParam['Weight'];
        }

        return $weight;
    }

    /**
     * @param array $a
     * @param array $b
     * @return mixed
     */
    public function sortByOrder($a, $b)
    {
        return $a[0]['ShippingCharge'] - $b[0]['ShippingCharge'];
    }

    /**
     * @param array $services
     * @return mixed
     */
    public function sortServicesByPrice($services)
    {
        uasort($services, 'self::sortByOrder');

        return $services;
    }

    /**
     * @param array  $address
     * @param string $email
     * @return array
     */
    public function formatCustomerAddress($address, $email)
    {
        $phone = $address['phone'] ? $address['phone'] : $address['phone_mobile'];
        $company = $address['company'] ? $address['company'] : $address['firstname'].' '.$address['lastname'];
        $address2 = $address['address2'] ? Tools::substr($address['address2'], 0, 35) : '';

        return array(
            'id_address'   => (int) $address['id'],
            'alias'        => $address['alias'],
            'company_name' => Tools::substr($company, 0, 35),
            'person_name'  => Tools::substr($address['firstname'].' '.$address['lastname'], 0, 35),
            'address1'     => Tools::substr($address['address1'], 0, 35),
            'address2'     => $address2,
            'address3'     => '',
            'zipcode'      => $address['postcode'],
            'city'         => Tools::substr($address['city'], 0, 35),
            'id_country'   => (int) $address['id_country'],
            'id_state'     => (int) $address['id_state'],
            'phone'        => $phone,
            'email'        => $email,
        );
    }

    /**
     * @param DhlShipmentValidationRequest $shipmentRequest
     */
    public function setArchiveDoc(DhlShipmentValidationRequest &$shipmentRequest)
    {
        if (!$this->data['form']['id_return_label']) {
            $destinationType = DhlTools::getDestinationType(
                $this->data['sender']->iso_country,
                $this->data['customer']['country'],
                $this->data['customer']['zipcode']
            );
        } else {
            $destinationType = DhlTools::getDestinationType(
                $this->data['customer']['country'],
                $this->data['sender']->iso_country,
                $this->data['sender']->zipcode
            );
        }
        $country_code_customer = $this->data['customer']['country'];
        $country_code_sender   = $this->data["sender"]->iso_country;
        $zip_code_sender   = $this->data["sender"]->zipcode;
        if (in_array($this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender), array(self::PLT_NOT_AVAILABLE, self::PLT_AVAILABLE_NOT_ELIGIBLE))) {
            if ($destinationType == 'WORLDWIDE') {
                $this->logger->info('Set archive doc to "Y" (worldwide shipment)');
                $shipmentRequest->setRequestArchiveDoc('Y');
            } else {
                if ($this->data['options']['archive_doc']) {
                    $this->logger->info('Set archive doc to "Y" (form option)');
                    $shipmentRequest->setRequestArchiveDoc('Y');
                }
            }
        } else {
            $shipmentRequest->setRequestArchiveDoc('N');
        }
    }

    public function setArchiveDocPlt(DhlShipmentValidationPltRequest &$shipmentRequest)
    {
        if (!$this->data['form']['id_return_label']) {
            $destinationType = DhlTools::getDestinationType(
                $this->data['sender']->iso_country,
                $this->data['customer']['country'],
                $this->data['customer']['zipcode']
            );
        } else {
            $destinationType = DhlTools::getDestinationType(
                $this->data['customer']['country'],
                $this->data['sender']->iso_country,
                $this->data['sender']->zipcode
            );
        }
        if ($destinationType == 'WORLDWIDE') {
            $this->logger->info('Set archive doc to "Y" (worldwide shipment)');
            $shipmentRequest->setRequestArchiveDoc('N');
        } else {
            if ($this->data['options']['archive_doc']) {
                $this->logger->info('Set archive doc to "Y" (form option)');
                $shipmentRequest->setRequestArchiveDoc('N');
            }
        }
    }

    /**
     * @param DhlShipmentValidationRequest $shipmentRequest
     */
    public function setShipmentSenderConsignee(DhlShipmentValidationRequest &$shipmentRequest)
    {
        if ($this->data['form']['id_return_label']) {
            $shipmentRequest->setConsignee(
                array(
                    'CompanyName' => $this->data['sender']->company_name,
                    'AddressLine1' => array(
                        $this->data['sender']->address1,
                        $this->data['sender']->address2,
                        $this->data['sender']->address3,
                    ),
                    'City'        => $this->data['sender']->city,
                    'PostalCode'  => $this->data['sender']->zipcode,
                    'CountryCode' => $this->data['sender']->iso_country,
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        $this->data['sender']->id_country
                    ),
                )
            );
            $shipmentRequest->setConsigneeContact(
                array(
                    'PersonName'  => $this->data['sender']->contact_name,
                    'PhoneNumber' => $this->data['sender']->contact_phone,
                    'Email'       => $this->data['sender']->contact_email,
                )
            );
            $shipmentRequest->setShipper(
                array(
                    'ShipperID'   => '1',
                    'CompanyName' => $this->data['customer']['company_name'] ? $this->data['customer']['company_name'] :
                        $this->data['customer']['person_name'],
                    'AddressLine1' => array(
                        0 => $this->data['customer']['address1'],
                        1 => $this->data['customer']['address2'],
                        2 => $this->data['customer']['address3'],
                    ),
                    'City'        => $this->data['customer']['city'],
                    'PostalCode'  => $this->data['customer']['zipcode'],
                    'CountryCode' => $this->data['customer']['country'],
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        (int) $this->data['customer']['id_country']
                    ),
                )
            );
            $shipmentRequest->setContactShipper(
                array(
                    'PersonName'  => $this->data['customer']['person_name'],
                    'PhoneNumber' => $this->data['customer']['phone'],
                    'Email'       => $this->data['customer']['email'],
                )
            );
            $extrachargeLabelLifetime = DhlTools::getLabelLifetimeExtracharge();
            $shipmentRequest->setSpecialService(array($extrachargeLabelLifetime));
        } else {
            $shipmentRequest->setConsignee(
                array(
                    'CompanyName' => $this->data['customer']['company_name'] ? $this->data['customer']['company_name'] :
                        $this->data['customer']['person_name'],
                    'AddressLine1' => array(
                        0 => $this->data['customer']['address1'],
                        1 => $this->data['customer']['address2'],
                        2 => $this->data['customer']['address3'],
                    ),
                    'City'        => $this->data['customer']['city'],
                    'PostalCode'  => $this->data['customer']['zipcode'],
                    'CountryCode' => $this->data['customer']['country'],
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        (int) $this->data['customer']['id_country']
                    ),
                )
            );
            $shipmentRequest->setConsigneeContact(
                array(
                    'PersonName'  => $this->data['customer']['person_name'],
                    'PhoneNumber' => $this->data['customer']['phone'] ? $this->data['customer']['phone'] : '0000000000',
                    'Email'       => $this->data['customer']['email'],
                )
            );
            $shipmentRequest->setShipper(
                array(
                    'ShipperID'   => '1',
                    'CompanyName' => $this->data['sender']->company_name,
                    'AddressLine1' => array(
                        $this->data['sender']->address1,
                        $this->data['sender']->address2,
                        $this->data['sender']->address3,
                    ),
                    'City'        => $this->data['sender']->city,
                    'PostalCode'  => $this->data['sender']->zipcode,
                    'CountryCode' => $this->data['sender']->iso_country,
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        $this->data['sender']->id_country
                    ),
                )
            );
            $shipmentRequest->setContactShipper(
                array(
                    'PersonName'  => $this->data['sender']->contact_name,
                    'PhoneNumber' => $this->data['sender']->contact_phone,
                    'Email'       => $this->data['sender']->contact_email,
                )
            );
        }
    }

    public function setShipmentSenderConsigneeUsingPlt(DhlShipmentValidationpltRequest &$shipmentRequest)
    {
        if ($this->data['form']['id_return_label']) {
            $shipmentRequest->setConsignee(
                array(
                    'CompanyName' => $this->data['sender']->company_name,
                    'AddressLine1' => array(
                        $this->data['sender']->address1,
                        $this->data['sender']->address2,
                        $this->data['sender']->address3,
                    ),
                    'City'        => $this->data['sender']->city,
                    'PostalCode'  => $this->data['sender']->zipcode,
                    'CountryCode' => $this->data['sender']->iso_country,
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        $this->data['sender']->id_country
                    ),
                )
            );
            $shipmentRequest->setConsigneeContact(
                array(
                    'PersonName'  => $this->data['sender']->contact_name,
                    'PhoneNumber' => $this->data['sender']->contact_phone,
                    'Email'       => $this->data['sender']->contact_email,
                )
            );
            $shipmentRequest->setShipper(
                array(
                    'ShipperID'   => '1',
                    'CompanyName' => $this->data['customer']['company_name'] ? $this->data['customer']['company_name'] :
                        $this->data['customer']['person_name'],
                    'AddressLine1' => array(
                        0 => $this->data['customer']['address1'],
                        1 => $this->data['customer']['address2'],
                        2 => $this->data['customer']['address3'],
                    ),
                    'City'        => $this->data['customer']['city'],
                    'PostalCode'  => $this->data['customer']['zipcode'],
                    'CountryCode' => $this->data['customer']['country'],
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        (int) $this->data['customer']['id_country']
                    ),
                )
            );
            $shipmentRequest->setContactShipper(
                array(
                    'PersonName'  => $this->data['customer']['person_name'],
                    'PhoneNumber' => $this->data['customer']['phone'],
                    'Email'       => $this->data['customer']['email'],
                )
            );
            $extrachargeLabelLifetime = DhlTools::getLabelLifetimeExtracharge();
            $shipmentRequest->setSpecialService(array($extrachargeLabelLifetime));
        } else {
            $shipmentRequest->setConsignee(
                array(
                    'CompanyName' => $this->data['customer']['company_name'] ? $this->data['customer']['company_name'] :
                        $this->data['customer']['person_name'],
                    'AddressLine1' => array(
                        0 => $this->data['customer']['address1'],
                        1 => $this->data['customer']['address2'],
                        2 => $this->data['customer']['address3'],
                    ),
                    'City'        => $this->data['customer']['city'],
                    'PostalCode'  => $this->data['customer']['zipcode'],
                    'CountryCode' => $this->data['customer']['country'],
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        (int) $this->data['customer']['id_country']
                    ),
                )
            );
            $shipmentRequest->setConsigneeContact(
                array(
                    'PersonName'  => $this->data['customer']['person_name'],
                    'PhoneNumber' => $this->data['customer']['phone'] ? $this->data['customer']['phone'] : '0000000000',
                    'Email'       => $this->data['customer']['email'],
                )
            );
            $shipmentRequest->setShipper(
                array(
                    'ShipperID'   => '1',
                    'CompanyName' => $this->data['sender']->company_name,
                    'AddressLine1' => array(
                        $this->data['sender']->address1,
                        $this->data['sender']->address2,
                        $this->data['sender']->address3,
                    ),
                    'City'        => $this->data['sender']->city,
                    'PostalCode'  => $this->data['sender']->zipcode,
                    'CountryCode' => $this->data['sender']->iso_country,
                    'CountryName' => Country::getNameById(
                        $this->context->language->id,
                        $this->data['sender']->id_country
                    ),
                )
            );
            $shipmentRequest->setContactShipper(
                array(
                    'PersonName'  => $this->data['sender']->contact_name,
                    'PhoneNumber' => $this->data['sender']->contact_phone,
                    'Email'       => $this->data['sender']->contact_email,
                )
            );
        }
    }

    /**
     * @param DhlLabel $dhlReturnLabelGenerated
     * @throws Exception
     * @throws SmartyException
     */
    public function displayGeneratedReturnLabel(DhlLabel $dhlReturnLabelGenerated)
    {
        $dhlService = new DhlService((int) $dhlReturnLabelGenerated->id_dhl_service);
        $this->context->smarty->assign(
            array(
                'errors'           => false,
                'id_dhl_label'     => $dhlReturnLabelGenerated->id,
                'alreadyGenerated' => true,
                'freeLabel'        => false,
                'labelDetails'     => array(
                    'GlobalProductCode' => $dhlService->global_product_code,
                    'ProductShortName'  => $dhlService->global_product_name,
                    'AirwayBillNumber'  => $dhlReturnLabelGenerated->awb_number,
                    'LabelImage'        => array(
                        'OutputFormat' => '',
                        'OutputImage'  => $dhlReturnLabelGenerated->label_string,
                    ),
                ),
                'link'             => $this->context->link,
                'dhl_img_path'     => $this->module->getPathUri().'views/img/',
            )
        );
        $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
        $return = array(
            'html'   => $html,
            'errors' => true,
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    /**
     * @param DhlService $dhlServiceObj
     * @param array      $dhlServices
     * @return string
     */
    public function getServiceWantedCode(DhlService $dhlServiceObj, $dhlServices)
    {
        foreach ($dhlServices as $code => $dhlService) {
            if (trim($code) == $dhlServiceObj->global_product_code) {
                return $dhlServiceObj->global_product_code;
            }
        }

        return '';
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function buildFormData()
    {
        $dhlOrder = DhlOrder::getByIdOrder((int) Tools::getValue('dhl_id_order'));
        $productCode = Tools::getValue('dhl_label_service');
        $globalProductCode = Tools::substr($productCode, 0, strpos($productCode, '_'));
        $this->data = array(
            'form'             => array(
                'free_label'      => Tools::getValue('dhl_free_label'),
                'id_order'        => Tools::getValue('dhl_id_order'),
                'id_return_label' => Tools::getValue('dhl_id_return_label'),
                'id_dhl_order'    => Validate::isLoadedObject($dhlOrder) ? $dhlOrder->id : 0,
            ),
            'sender'           => new DhlAddress((int) Tools::getValue('dhl_sender_address')),
            'options'          => array(
                'iso_currency'            => Tools::getValue('dhl_label_currency_iso'),
                'declared_value_currency' => Tools::getValue('dhl_label_currency_iso'),
                'insured_value_currency'  => Tools::getValue('dhl_label_currency_iso'),
                'reference'               => Tools::getValue('dhl_reference_id'),
                'contents'                => Tools::getValue('dhl_label_contents'),
                'doc'                     => Tools::getValue('dhl_sending_doc'),
                'declared_value'          => Tools::getValue('dhl_label_declared_value'),
                'insured_value'           => Tools::getValue('dhl_label_insured_value'),
                'global_product_code'     => $globalProductCode,
                'local_product_code'      => Tools::getValue('dhl_label_local_code_'.$productCode),
                'archive_doc'             => (int) Tools::getValue('dhl_print_doc_archive'),
            ),
            'packages'         => $this->getProductsParams(Tools::getValue('package_dimensions')),
            'extracharges'     => $this->getChosenExtracharges(),
            'doc_extracharges' => $this->getChosenExtracharges(true),
            'dhl_number_pieces_concerned-3'     =>  Tools::getValue('dhl-number-pieces-concerned-3'),
            'dhl_number_pieces_concerned-4'     =>  Tools::getValue('dhl-number-pieces-concerned-4'),
            'dhl_number_pieces_concerned-5'     =>  Tools::getValue('dhl-number-pieces-concerned-5'),
            'dhl_number_pieces_concerned-6'     =>  Tools::getValue('dhl-number-pieces-concerned-6'),
            'dhl_number_pieces_concerned-7'     =>  Tools::getValue('dhl-number-pieces-concerned-7'),
            'dhl_number_pieces_concerned-8'     =>  Tools::getValue('dhl-number-pieces-concerned-8'),
            'dhl_number_pieces_concerned-9'     =>  Tools::getValue('dhl-number-pieces-concerned-9'),
            'dhl_number_pieces_concerned-11'    =>  Tools::getValue('dhl-number-pieces-concerned-11'),
            'total_pieces_concerned'            =>  Tools::getValue('dhl-total-pieces-concerned2'),
        );
        if ($this->data['form']['free_label']) {
            $this->data['customer'] = array(
                'company_name' => Tools::getValue('company_name'),
                'person_name'  => Tools::getValue('person_name'),
                'address1'     => Tools::getValue('address1'),
                'address2'     => Tools::getValue('address2'),
                'address3'     => Tools::getValue('address3'),
                'zipcode'      => Tools::getValue('zipcode'),
                'city'         => Tools::getValue('city'),
                'id_country'   => Tools::getValue('id_country'),
                'id_state'     => Tools::getValue('id_state'),
                'phone'        => Tools::getValue('phone'),
                'email'        => Tools::substr(Tools::getValue('email'), 0, 50),
                'country'      => DhlTools::getIsoCountryById(Tools::getValue('id_country')),
            );
        } else {
            $address = new Address((int) Tools::getValue('dhl_customer_address'));
            $country = new Country((int) $address->id_country);
            $state = new State((int) $address->id_state);
            $customer = new Customer((int) $address->id_customer);
            $this->data['customer'] = array(
                'company_name' => $address->company,
                'person_name'  => $address->lastname.' '.$address->firstname,
                'address1'     => $address->address1,
                'address2'     => $address->address2,
                'address3'     => '',
                'zipcode'      => $address->postcode,
                'city'         => $address->city,
                'id_country'   => $country->id,
                'id_state'     => $state->id,
                'phone'        => $address->phone ? $address->phone : $address->phone_mobile,
                'email'        => Tools::substr($customer->email, 0, 50),
                'country'      => DhlTools::getIsoCountryById((int) $country->id),
            );
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws Exception
     */
    public function buildProductsData()
    {
        if (Tools::getValue('dhl_id_order')){
            $idDhlOrder = Tools::getValue('dhl_id_order');
        } else {
            $idDhlOrder = Tools::getValue('id_dhl_order');
        }
        $dhlOrder = DhlOrder::getByIdOrder((int) $idDhlOrder);
        $isoFrom = $this->data['sender']->iso_country;
        $isoTo = $this->data['customer']['country'];
        $pcTo = $this->data['customer']['zipcode'];
        $weightUnit = DhlTools::getWeightUnit();
        if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo) && !$this->data['form']['free_label'] && !$this->data['form']['id_return_label']) {
            $orderDetails = OrderDetail::getList((int) $dhlOrder->id_order );
            foreach ($orderDetails as $orderDetail) {
                if (Tools::isSubmit('dhl_od_weight_'.(int) $orderDetail['id_order_detail']) || Tools::isSubmit('dhl_use_order_weight')) {
                    $pname = Tools::getValue('dhl_od_pname_'.(int)$orderDetail['id_order_detail']) ? Tools::getValue('dhl_od_pname_'.(int)$orderDetail['id_order_detail']) : $orderDetail['product_name'];
                    $country = Tools::getValue('dhl_od_country_'.(int)$orderDetail['id_order_detail']) ? Country::getIsoById((int)Tools::getValue('dhl_od_country_'.(int)$orderDetail['id_order_detail'])) : 'FR';
                    $unitPrice = Tools::getValue('dhl_od_price_'.(int)$orderDetail['id_order_detail']) ? Tools::getValue('dhl_od_price_'.(int)$orderDetail['id_order_detail']) : $orderDetail['unit_price_tax_excl'];
                    $qty = Tools::getValue('dhl_od_quantity_'.(int)$orderDetail['id_order_detail']) ? Tools::getValue('dhl_od_quantity_'.(int)$orderDetail['id_order_detail']) : $orderDetail['product_quantity'];
                    $weight = Tools::getValue('dhl_od_weight_'.(int)$orderDetail['id_order_detail']) ? Tools::getValue('dhl_od_weight_'.(int)$orderDetail['id_order_detail']) : $orderDetail['product_weight'];
                    $commodityCode = Tools::getValue('dhl_od_hs_code_'.(int)$orderDetail['id_order_detail']) ? Tools::getValue('dhl_od_hs_code_'.(int)$orderDetail['id_order_detail']) : Configuration::get('DHL_DEFAULT_HS_CODE');
                    $totalPrice = (int)$qty * (float)$unitPrice;
                    $productVars[] = array(
                        'product_name' => $pname,
                        'product_quantity' => (int)$qty,
                        'unit_price_tax_excl' => (float)Tools::ps_round($unitPrice, 2),
                        'total_price_tax_excl' => (float)$totalPrice,
                        'product_weight' => (float)$weight,
                        'product_total_weight' => (int)$qty * (float)$weight,
                        'weight_unit' => $weightUnit == 'kg' ? 'K' : 'L',
                        'origin' => $country,
                        'commodity_code' => $commodityCode,
                    );
                    if ($weight == 0 && Tools::isSubmit('dhl_od_weight_'.(int)$orderDetail['id_order_detail'])) {
                        throw new Exception($this->module->l('Products weight must be filled to generate your label. A weight greater than 0 is required. If your package weights only 10 grams, please fill 0.01',
                            'AdminDhlLabel'));
                    }
                }
            }
            $posts = array_keys(Tools::getAllValues());
            foreach ($posts as $postKey) {
                if (Tools::substr($postKey, 0, 17) == 'dhl_supp_country_') {
                    $id = (int) Tools::substr($postKey, 17);
                    $country = (int) Country::getIdByName($this->context->language->id, Tools::getValue('dhl_supp_country_'.(int) $id));
                    $unitPrice = Tools::ps_round(Tools::getValue('dhl_supp_price_'.(int) $id), 2);
                    $qty = Tools::getValue('dhl_supp_quantity_'.(int) $id);
                    $weight = Tools::getValue('dhl_supp_weight_'.(int) $id);
                    $subtotalWeight = (float) $weight * (int) $qty;
                    $totalPrice = (int) $qty * (float) $unitPrice;
                    $productVars[] = array(
                        'product_name'         => Tools::getValue('dhl_supp_pname_'.(int) $id),
                        'product_quantity'     => (int) $qty,
                        'unit_price_tax_excl'  => (float) $unitPrice,
                        'total_price_tax_excl' => (float) $totalPrice,
                        'product_weight'       => (float) $weight,
                        'product_total_weight' => (float) $subtotalWeight,
                        'weight_unit'          => $weightUnit == 'kg' ? 'K' : 'L',
                        'origin'               => Country::getIsoById($country),
                        'commodity_code'       => Tools::getValue('dhl_supp_hs_code_'.(int) $id),
                    );
                    if ($weight == 0) {
                        throw new Exception($this->module->l('Products weight must be filled to generate your label. A weight greater than 0 is required. If your package weights only 10 grams, please fill 0.01', 'AdminDhlLabel'));
                    }
                }
            }
            $this->data['products'] = $productVars;
        }
    }

    /**
     *
     */
    public function buildBulkShipmentData()
    {
        $dhlOrder = new DhlOrder((int) Tools::getValue('id_dhl_order'));
        $order = new Order((int) $dhlOrder->id_order);
        $orderCurrency = new Currency((int) $order->id_currency);
        $defaultCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $extracharges = array();
        $insuredValueCurrency = $defaultCurrency->iso_code;
        if (Tools::getValue('dhl_insure_shipment') && Tools::getValue('dhl_insured_value')) {
            $insuredValue = Tools::getValue('dhl_insured_value');
            $idExtrachargeInsurance = DhlExtracharge::getIdByCode('II');
            $extrachargeInsurance = new DhlExtracharge((int) $idExtrachargeInsurance);
            $extracharges[$extrachargeInsurance->extracharge_code] = $extrachargeInsurance->label;
        } else {
            $insuredValue = 0;
        }
        $this->data = array(
            'form'             => array(
                'free_label'      => false,
                'id_order'        => $order->id,
                'id_return_label' => false,
                'id_dhl_order'    => $dhlOrder->id,
            ),
            'sender'           => new DhlAddress((int) Configuration::get('DHL_DEFAULT_SENDER_ADDRESS')),
            'options'          => array(
                'iso_currency'           => $orderCurrency->iso_code,
                'insured_value_currency' => $insuredValueCurrency,
                'reference'              => Tools::getValue('dhl_order_identifier') == 'reference' ? $order->reference : $order->id,
                'contents'               => Tools::getValue('dhl_contents'),
                'doc'                    => false,
                'insured_value'          => $insuredValue,
                'archive_doc'            => (int) Tools::getValue('dhl_print_doc_archive'),
                'local_product_code'     => false,
            ),
            'packages'         => $this->getBulkProductsParams($order->getTotalWeight()),
            'extracharges'     => $extracharges,
            'doc_extracharges' => array(),
            'dhl_number_pieces_concerned-3'     =>  0,
            'dhl_number_pieces_concerned-4'     =>  0,
            'dhl_number_pieces_concerned-5'     =>  0,
            'dhl_number_pieces_concerned-6'     =>  0,
            'dhl_number_pieces_concerned-7'     =>  0,
            'dhl_number_pieces_concerned-8'     =>  0,
            'dhl_number_pieces_concerned-9'     =>  0,
            'dhl_number_pieces_concerned-11'    =>  0,
            'total_pieces_concerned'            =>  0,
        );
        $address = new Address((int) $order->id_address_delivery);
        $country = new Country((int) $address->id_country);
        $state = new State((int) $address->id_state);
        $customer = new Customer((int) $address->id_customer);
        $this->data['customer'] = array(
            'company_name' => $address->company,
            'person_name'  => $address->lastname.' '.$address->firstname,
            'address1'     => $address->address1,
            'address2'     => $address->address2,
            'address3'     => '',
            'zipcode'      => $address->postcode,
            'city'         => $address->city,
            'id_country'   => $country->id,
            'id_state'     => $state->id,
            'phone'        => $address->phone ? $address->phone : $address->phone_mobile,
            'email'        => Tools::substr($customer->email, 0, 50),
            'country'      => DhlTools::getIsoCountryById((int) $country->id),
        );
        if (Tools::getValue('dhl_use_declared_value')) {
            $isoFrom = $this->data['sender']->iso_country;
            $isoTo = $this->data['customer']['country'];
            $pcTo  = $this->data['customer']['zipcode'];
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo)) {
                $this->data['options']['declared_value'] = (int) $order->total_products;
            } else {
                $this->data['options']['declared_value'] = 0;
            }
            $this->data['options']['declared_value_currency'] = $orderCurrency->iso_code;
        } else {
            $this->data['options']['declared_value_currency'] = $defaultCurrency->iso_code;
            if (Tools::getValue('dhl_declared_value')) {
                $this->data['options']['declared_value'] = Tools::getValue('dhl_declared_value');
            } else {
                $this->data['options']['declared_value'] = 0;
            }
        }
        $useDhlService = Tools::getValue('dhl_use_dhl_service');
        if ($useDhlService) {
            $idDhlService = $dhlOrder->id_dhl_service;
        } else {
            if ($country->iso_code == 'FR') {
                $idDhlService = Tools::getValue('dhl_services_domestic');
            } elseif (DhlTools::isEUCountry($country->iso_code)) {
                $idDhlService = Tools::getValue('dhl_services_europe');
            } else {
                $idDhlService = Tools::getValue('dhl_services_world');
            }
        }
        $dhlService = new DhlService((int) $idDhlService);
        $this->data['options']['global_product_code'] = $dhlService->global_product_code;
    }

    /**
     * @throws DhlException
     */
    public function validateFreeAddress()
    {
        $idCountry = $this->data['customer']['id_country'];
        $idState = $this->data['customer']['id_state'];
        $country = new Country((int) $idCountry);
        $errors = array();
        if ($country && !(int) $country->contains_states && $idState) {
            $errors[] = $this->l('You have selected a state for a country that does not contain states.');
        }
        if ((int) $country->contains_states && !$idState) {
            $errors[] = $this->l('An address located in a country containing states must have a state selected.');
        }
        $zipcode = $this->data['customer']['zipcode'];
        if (empty($zipcode) && $country->need_zip_code) {
            $errors[] = $this->l('A Zip/postal code is required.');
        } 
        $requiredFields = array(
            'company_name' => $this->module->l('company_name', 'AdminDhlLabel'),
            'person_name'  => $this->module->l('person_name', 'AdminDhlLabel'),
            'address1'     => $this->module->l('address1', 'AdminDhlLabel'),
            'city'         => $this->module->l('city', 'AdminDhlLabel'),
            'phone'        => $this->module->l('phone', 'AdminDhlLabel'),
        );
        foreach ($requiredFields as $key => $requiredField) {
            if (!$this->data['customer'][$key]) {
                $errors[] = sprintf($this->l('The field %s is required in customer address.'), $requiredField);
            }
        }
        if (!empty($errors)) {
            throw new DhlException('Errors found while validating address.', 0, null, $errors);
        }
    }

    /**
     * @throws DhlException
     * @throws Exception
     * @throws SmartyException
     */
    public function validateRequest()
    {
        if ($this->data['form']['free_label']) {
            $this->validateFreeAddress();
        }
        if ($this->data['form']['id_return_label']) {
            $idReturnLabel = $this->data['form']['id_return_label'];
            $dhlLabelOrigin = new DhlLabel((int) $idReturnLabel);
            if ((int) $idReturnLabel && !Validate::isLoadedObject($dhlLabelOrigin)) {
                $this->logger->error('Return label. Origin label is not valid.');
                throw new DhlException($this->module->l('Invalid label.', 'AdminDhlLabel'));
            }
            $dhlReturnLabelGenerated = $dhlLabelOrigin->getDhlReturnLabel();
            if (Validate::isLoadedObject($dhlReturnLabelGenerated)) {
                $this->displayGeneratedReturnLabel($dhlReturnLabelGenerated);
            }
        }
        if (!Validate::isLoadedObject($this->data['sender'])) {
            $this->logger->error('Invalid sender address.');
            throw new DhlException($this->module->l('Invalid sender address.', 'AdminDhlLabel'));
        }
        if (empty($this->data['packages'])) {
            $this->logger->error('Missing packages.');
            throw new DhlException($this->module->l('You must add one package at least.', 'AdminDhlLabel'));
        }
        if (!$this->data['options']['reference']) {
            $this->logger->error('Missing Reference ID.');
            throw new DhlException($this->module->l('You must fill a Reference ID', 'AdminDhlLabel'), 0, null);
        }
        if (!$this->data['options']['contents']) {
            $this->logger->error('Missing shipping contents.');
            throw new DhlException($this->module->l('You must specify a description of the content', 'AdminDhlLabel'));
        }
        if (!$this->data['options']['doc']) {
            $isoFrom = $this->data['sender']->iso_country;
            $isoTo = $this->data['customer']['country'];
            $pcTo = $this->data['customer']['zipcode'];
            $declaredValue = $this->data['options']['declared_value'];
            $insuredValue = $this->data['options']['insured_value'];
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo) && (int) !$declaredValue) {
                $this->logger->error('This shipment requires you to declare the goods value.');
                // @formatter:off
                throw new DhlException(
                    $this->module->l('This shipment requires you to declare the goods value.', 'AdminDhlLabel')
                );
                // @formatter:on
            }
            if (array_key_exists('II', $this->data['extracharges']) && (!$declaredValue || !$insuredValue)) {
                $this->logger->error('Merchant chose insurance extracharge. Missing declared/goods value.');
                // @formatter:off
                throw new DhlException(
                    $this->module->l('You chose the shipment insurance extracharge, therefore you need to declare both the goods value and the insured value.', 'AdminDhlLabel')
                );
                // @formatter:on
            }
            if (array_key_exists('DD', $this->data['extracharges']) && !$this->data['sender']->account_duty) {
                $this->logger->error('Missing DHL duty account number');
                // @formatter:off
                throw new DhlException(
                    $this->module->l('Please complete your DHL duty account number for the chosen shipping address.', 'AdminDhlLabel')
                );
                // @formatter:on
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-3'] && array_key_exists('HE', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned FULL IATA.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-3'] && array_key_exists('HE', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-3'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned FULL IATA is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-4'] && array_key_exists('HH', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Excepted quantities.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-4'] && array_key_exists('HH', $this->data['extracharges']))&& $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-4'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Excepted quantities is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-5'] && array_key_exists('HB', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Lithium Ion PI965 Section II.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-5'] && array_key_exists('HB', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-5'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Lithium Ion PI965 Section II is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-6'] && array_key_exists('HD', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Lithium Ion PI966 Section II.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-6'] && array_key_exists('HD', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-6'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Lithium Ion PI966 Section II is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-7'] && array_key_exists('HV', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Lithium Ion PI967-Section II.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-7'] && array_key_exists('HV', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-7'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Lithium Ion PI967-Section II is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-8'] && array_key_exists('HM', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Lithium Metal PI969 Section II.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-8'] && array_key_exists('HM', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-8'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Lithium Metal PI969 Section II is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-9'] && array_key_exists('HW', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned Lithium Metal PI970-Section II .', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-9'] && array_key_exists('HW', $this->data['extracharges'])) && $this->data['extracharges']) {
            $total_pieces_concerned = $this->data['total_pieces_concerned'];
            $nbr_pieces_declared   = $this->data['dhl_number_pieces_concerned-9'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces concerned is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned Lithium Metal PI970-Section II is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if ((!$this->data['dhl_number_pieces_concerned-11'] && array_key_exists('HK', $this->data['extracharges'])) && $this->data['extracharges']) {
            $this->logger->error('Missing number of pieces concerned.');
            throw new DhlException($this->module->l('You must fill the number of pieces concerned ID 800.', 'AdminDhlLabel'));
        }
        
        if (($this->data['dhl_number_pieces_concerned-11'] && array_key_exists('HK', $this->data['extracharges']))&& $this->data['extracharges']) {
            $total_pieces_concerned = (int) $this->data['total_pieces_concerned'];
            $nbr_pieces_declared    = (int) $this->data['dhl_number_pieces_concerned-11'];
            if ($nbr_pieces_declared > $total_pieces_concerned) {
                $this->logger->error('The number of pieces ID 800 is not valid, it must be less than the total number of packages.');
                throw new DhlException($this->module->l('The number of pieces concerned ID 800 is not valid, it must be less than the total number of packages.', 'AdminDhlLabel'));
            }
        }
        
        if (isset($this->data['products']) && !$this->data['options']['doc']) {
            foreach ($this->data['products'] as $product) {
                if ($product['product_name'] == "" || $product['product_quantity'] == 0 || $product['unit_price_tax_excl'] == 0) {
                    $this->logger->error('Missing Product informations');
                    //@formatter:off
                    throw new DhlException(
                        $this->module->l('Please fill in all the product informations.', 'AdminDhlLabel')
                    );
                    // @formatter:on
                }
            }
        }
                
    }

    /**
     * @throws DhlException
     * @throws Exception
     * @throws PrestaShopException
     */
    public function generateLabel()
    {
        $this->logger->info('Generating label.');
        $credentials = DhlTools::getCredentials();
        $shipmentRequest = new DhlShipmentValidationRequest($credentials);
        $country_code_customer = $this->data['customer']['country'];
        $country_code_sender   = $this->data["sender"]->iso_country;
        $zip_code_sender   = $this->data["sender"]->zipcode;

        if ($this->data['form']['free_label']) {
            if ($this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender) !== self::PLT_NOT_AVAILABLE) {
                throw new DhlException($this->module->l('Editing label to destinations eligible for PLT must be done from "Orders".', 'AdminDhlLabel'));
            }
        }
        if ($this->data['form']['id_return_label']) {
            $accountNumber =
                $this->data['sender']->getReturnShippingAccountNumber((int) $this->data['customer']['id_country']);
        } else {
            $accountNumber = $this->data['sender']->getAccountNumber();
        }
        $shipmentRequest->setBilling(
            array(
                'ShipperAccountNumber' => $accountNumber,
            )
        );
        $isoFrom = $this->data['sender']->iso_country;
        $isoTo = $this->data['customer']['country'];
        $pcTo = $this->data['customer']['zipcode'];
        if ($this->data['options']['doc']) {
            $this->logger->info('"doc" shipment.');
            if (array_key_exists('IB', $this->data['doc_extracharges'])) {
                $shipmentRequest->setSpecialService(array_keys($this->data['doc_extracharges']));
            }
            if (array_key_exists('HE', $this->data['extracharges'])) {
                $shipmentRequest->setSpecialService(array_keys($this->data['extracharges']));
                $dgCodes[] = DhlExtracharge::getDgCodeByCode('HE');
                $shipmentRequest->setDangerousCode($dgCodes, '');
            }               
        } else {
            $this->logger->info('Not a "doc" shipment.');
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo)) {
                $shipmentRequest->setIsDutiable('Y');
            }
            if ($this->data['options']['declared_value']) {

                if (array_key_exists('DD', $this->data['extracharges'])) {
                    $shipmentRequest->setDutyActivated(
                        array(
                                'DeclaredValue'    => $this->data['options']['declared_value'],
                                'DeclaredCurrency' => $this->data['options']['declared_value_currency'],
                                'TermsOfTrade'     =>'DDP',
                        )
                    );
                } else {
                    $shipmentRequest->setDuty(
                        array(
                                'DeclaredValue'    => $this->data['options']['declared_value'],
                                'DeclaredCurrency' => $this->data['options']['declared_value_currency'],
                                'TermsOfTrade' => 'DAP',
                        )
                    );
                }
            }
            $dutyAccount = $this->data['sender']->account_duty;
            if (array_key_exists('DD', $this->data['extracharges'])) {
                $shipmentRequest->setBilling(
                    array(
                        'DutyAccountNumber' => $dutyAccount,
                    )
                );
            }
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo) &&
                !$this->data['form']['free_label'] &&
                !$this->data['form']['id_return_label']
            ) {
                $shipmentRequest->setEdatas(
                    array(
                        'UseDHLInvoice'  => Tools::getValue('dhl_plt_service') != "use_printed_invoice" ? 'Y' : 'N',
                        'SignatureName'  => Configuration::get('DHL_SIGNATURE_NAME'),
                        'SignatureTitle' => Configuration::get('DHL_SIGNATURE_TITLE'),
                        'InvoiceNumber'  => 'INV11227788',
                        'InvoiceDate'    => date('Y-m-d'),
                        'products'       => isset($this->data['products']) ? $this->data['products'] : '',
                    )
                );
            }
            if (!$this->data['form']['id_return_label'] && $this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender) === self::PLT_AVAILABLE) {
                $this->data['extracharges']['WY'] = 1;
            }
            $this->logger->info('Extracharges', $this->data['extracharges']);
            $extracharges = array_keys($this->data['extracharges']);
            $shipmentRequest->setSpecialService($extracharges, Tools::getValue('dhl_label_insured_value'), Tools::getValue('dhl_label_currency_iso'));
            $dgCodes = array();
            foreach ($extracharges as $extracharge){
                $extrachargeDgCode = DhlExtracharge::getDgCodeByCode($extracharge);
                if ($extrachargeDgCode) {
                    $dgCodes[] = $extrachargeDgCode;
                }
            }
            $codeUN = Tools::getValue('TYPE_DESIGNATION_UN_XXXX');
            if (!empty($dgCodes)) {
                $shipmentRequest->setDangerousCode(array_reverse($dgCodes), $codeUN);
            }
        }
        $shipmentRequest->setMetaDataVersion(sprintf('PS%s', _PS_VERSION_));
        $shipmentRequest->setLanguageCode('fr');
        $this->setShipmentSenderConsignee($shipmentRequest);
        $this->setArchiveDoc($shipmentRequest);
        $weightUnit = DhlTools::getWeightUnit();
        $dimensionUnit = DhlTools::getDimensionUnit();
        $totalWeight = $this->getTotalWeight($this->data['packages']);
        $shipmentRequest->setShipmentDetails(
            array(
             //   'NumberOfPieces'    => count($this->data['packages']),
                'Pieces'            => $this->data['packages'],
             //   'Weight'            => $totalWeight,
                'WeightUnit'        => $weightUnit == 'kg' ? 'K' : 'L',
                'GlobalProductCode' => $this->data['options']['global_product_code'],
                'LocalProductCode'  => $this->data['options']['local_product_code'],
                'Date'              => date('Y-m-d'),
                'Contents'          => $this->data['options']['contents'],
                'DimensionUnit'     => $dimensionUnit == 'cm' ? 'C' : 'I',
                'CurrencyCode'      => $this->data['options']['insured_value_currency'],
            )
        );
        $labelType = Configuration::get('DHL_LABEL_TYPE');
        $labelImageFormat =
            isset($this->labelFormat[$labelType]) ? $this->labelFormat[$labelType] : $this->labelFormat['pdfa4'];
        $shipmentRequest->setLabelImageFormat($labelImageFormat);
        $shipmentRequest->setReferenceID($this->data['options']['reference']);
        $client = new DhlClient((int) Configuration::get('DHL_LIVE_MODE'));
        $client->setRequest($shipmentRequest);
        $this->logger->logXmlRequest($shipmentRequest);
        $response = $client->request();
        if ($response && $response instanceof DhlShipmentValidationResponse) {
            $errors = $response->getErrors();
            $this->logger->info('Response received.', array('label_resp' => $response));
            if (empty($errors)) {
                $labelDetails = $response->getLabelDetails();
                if ($this->data['form']['id_dhl_order'] && !$this->data['form']['free_label']) {
                    $idDhlService = DhlService::getIdByProductCode(
                        $labelDetails['GlobalProductCode'],
                        $this->data['options']['doc']
                    );
                    $dhlOrder = new DhlOrder((int) $this->data['form']['id_dhl_order']);
                    $dhlLabel = new DhlLabel();
                    $serviceArea = $labelDetails['ServiceAreaCode'];
                    $countryName = $labelDetails['CountryName'];
                    $chargeableWeight = $labelDetails['ChargeableWeight'];
                    $dhlLabel->id_dhl_order = (int) $dhlOrder->id_dhl_order;
                    $dhlLabel->id_dhl_service = (int) $idDhlService;
                    $dhlLabel->awb_number = pSQL($labelDetails['AirwayBillNumber']);
                    $dhlLabel->order_reference = pSQL($labelDetails['AirwayBillNumber']);
                    $dhlLabel->return_label = (int) $this->data['form']['id_return_label'];
                    $dhlLabel->label_string = pSQL($labelDetails['LabelImage']['OutputImage']);
                    $dhlLabel->piece_contents = pSQL($labelDetails['Contents']);
                    $dhlLabel->total_pieces = (int) $labelDetails['Piece'];
                    $dhlLabel->total_weight = Tools::strtoupper((float) $chargeableWeight.$weightUnit);
                    $dhlLabel->consignee_contact = pSQL($labelDetails['PersonName']);
                    $dhlLabel->consignee_destination = pSQL($serviceArea.' / '.Tools::strtoupper($countryName));
                    if (!$dhlLabel->save()) {
                        $this->logger->error('Cannot save label to DB');
                        throw new DhlException($this->module->l('Cannot save label locally', 'AdminDhlLabel'));
                    } else {
                        $this->logger->info('Saving label to DB', array('label' => $dhlLabel));
                        if (isset($labelDetails['LabelImage']['MultiLabels']) && DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo) && !$this->data['options']['doc']) {
                            // save dhl invoice
                            $dhlCI = new DhlCommercialInvoice();
                            $dhlCI->id_dhl_label = $dhlLabel->id;
                            $dhlCI->id_dhl_order = (int) $dhlOrder->id_dhl_order;
                            $dhlCI->pdf_string = pSQL($labelDetails['LabelImage']['MultiLabels']);
                            if (!$dhlCI->save()) {
                                $this->logger->error('Cannot save dhl invoice to DB');
                            } else {
                                $this->logger->info('Saving dhl invoice to DB', array('invoice' => $dhlCI));
                            }
                        }
                        
                        // Order changes to "Handling of shipment in progress" if has not in the past
                        $idDhlOsPreparation = (int) Configuration::get('DHL_OS_PREPARATION');
                        $order = new Order((int) $this->data['form']['id_order']);
                        $orderHistory = $order->getHistory($order->id_lang, (int) $idDhlOsPreparation);
                        if (empty($orderHistory)) {
                            $history = new OrderHistory();
                            $history->id_order = (int) $order->id;
                            $history->changeIdOrderState($idDhlOsPreparation, (int) $order->id);
                            $history->addWithemail();
                            $this->logger->info('Sending mail "Handling shipment in progress"');
                        }
                        $subject = $this->module->l('Handling of shipment in progress', 'AdminDhlLabel');
                        DhlTools::sendHandlingShipmentMail($order, $subject, $dhlLabel->awb_number);
                        $country_code_customer = $this->data['customer']['country'];
                        $country_code_sender   = $this->data["sender"]->iso_country;
                        $zip_code_sender   = $this->data["sender"]->zipcode;
                        $service_plt = $this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender);
                        $return = array(
                            'errors'           => false,
                            'labelDetails'     => $labelDetails,
                            'plt'              => $service_plt,
                            'id_dhl_label'     => $dhlLabel->id,
                            'id_dhl_invoice'   => isset($dhlCI),
                            'alreadyGenerated' => false,
                            'freeLabel'        => false,
                        );
                        $this->logger->info('Displaying label details.', ['return' => $return]);

                        return $return;
                    }
                } elseif ($this->data['form']['free_label']) {
                    $this->logger->info('This is a free label. Displaying label details.');
                    $return = array(
                        'errors'           => false,
                        'labelDetails'     => $labelDetails,
                        'alreadyGenerated' => false,
                        'freeLabel'        => true,
                    );

                    return $return;
                } else {
                    $this->logger->error('Unexpected error.');
                    throw new DhlException($this->module->l('Unexpected error. Please try again.', 'AdminDhlLabel'));
                }
            } else {
                $this->logger->error('Errors found. Please review response.', array('errors' => $errors));
                throw new DhlException(str_replace('\\n', ' ', $errors['code'].' - '.$errors['text']));
            }
        } else {
            $this->logger->error('Cannot connect to DHL API.');
            throw new DhlException($this->module->l('Cannot connect to DHL API.', 'AdminDhlLabel'));
        }
    }
     /**
     * @throws DhlException
     * @throws Exception
     * @throws PrestaShopException
     */
    public function generateLabelAndInvoice()
    {
        require_once(dirname(__FILE__).'/../../classes/DhlCommercialInvoice.php');
        
        $this->logger->info('Generating label.');
        $credentials = DhlTools::getCredentials();
        $shipmentRequest = new DhlShipmentValidationPltRequest($credentials);
        
        if ($this->data['form']['id_return_label']) {
            $accountNumber =
                $this->data['sender']->getReturnShippingAccountNumber((int) $this->data['customer']['id_country']);
        } else {
            $accountNumber = $this->data['sender']->getAccountNumber();
        }
        $shipmentRequest->setBilling(
            array(
                'ShipperAccountNumber' => $accountNumber,
            )
        );

        if ($this->data['options']['doc']) {
            $this->logger->info('"doc" shipment.');
            if (array_key_exists('IB', $this->data['doc_extracharges'])) {
                $shipmentRequest->setSpecialService(array_keys($this->data['doc_extracharges']));
            }
            if (array_key_exists('HE', $this->data['extracharges'])) {
                $shipmentRequest->setSpecialService(array_keys($this->data['extracharges']));
                $dgCodes[] = DhlExtracharge::getDgCodeByCode('HE');
                $shipmentRequest->setDangerousCode($dgCodes, '');
            }
            $shipmentRequest->setSpecialService(array_keys($this->data['extracharges']), Tools::getValue('dhl_label_insured_value'), Tools::getValue('dhl_label_currency_iso'));
        } else {
            $this->logger->info('Not a "doc" shipment.');
            $isoFrom = $this->data['sender']->iso_country;
            $isoTo = $this->data['customer']['country'];
            $pcTo = $this->data['customer']['zipcode'];
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo)) {
                $shipmentRequest->setIsDutiable('Y');
            }
            //$shipmentRequest->setCommodityPlt();
            if ($this->data['options']['declared_value']) {
                if (array_key_exists('DD', $this->data['extracharges'])) {
                    $shipmentRequest->setDutyActivated(
                        array(
                                'DeclaredValue'    => $this->data['options']['declared_value'],
                                'DeclaredCurrency' => $this->data['options']['declared_value_currency'],
                                'TermsOfTrade'     =>'DDP',
                        )
                    );
                } else {
                    $shipmentRequest->setDutyDeactivated(
                        array(
                                'DeclaredValue'    => $this->data['options']['declared_value'],
                                'DeclaredCurrency' => $this->data['options']['declared_value_currency'],
                                'TermsOfTrade'     =>'DAP',
                        )
                    );
                }
            }
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo)) {
                $shipmentRequest->setEdatas(
                    array(
                        'UseDHLInvoice'  => Tools::getValue('dhl_plt_service') == "create_invoice" ? 'Y' : 'N',
                        'SignatureName'  => Configuration::get('DHL_SIGNATURE_NAME'),
                        'SignatureTitle' => Configuration::get('DHL_SIGNATURE_TITLE'),
                        'InvoiceNumber'  => 'INV11227788',
                        'InvoiceDate'    => date('Y-m-d'),
                        'products'       => isset($this->data['products']) ? $this->data['products'] : '',
                    )
                );
            }
            $dutyAccount = $this->data['sender']->account_duty;
            if (array_key_exists('DD', $this->data['extracharges'])) {
                $shipmentRequest->setBilling(
                    array(
                        'DutyAccountNumber' => $dutyAccount,
                    )
                );
            }
            $extracharges = array_keys($this->data['extracharges']);
            $shipmentRequest->setSpecialService($extracharges, Tools::getValue('dhl_label_insured_value'), Tools::getValue('dhl_label_currency_iso'));
            $dgCodes = array();
            foreach ($extracharges as $extracharge){
                $dgCodes[] = DhlExtracharge::getDgCodeByCode($extracharge);
            }
            $codeUN = Tools::getValue('TYPE_DESIGNATION_UN_XXXX');
            $shipmentRequest->setDangerousCode(array_reverse($dgCodes), $codeUN);
        }
        $shipmentRequest->setMetaDataVersion(sprintf('PS%s', _PS_VERSION_));
        $shipmentRequest->setLanguageCode('fr');
        $this->setShipmentSenderConsigneeUsingPlt($shipmentRequest);
        $this->setArchiveDocPlt($shipmentRequest);
        $weightUnit = DhlTools::getWeightUnit();
        $dimensionUnit = DhlTools::getDimensionUnit();
        $totalWeight = $this->getTotalWeight($this->data['packages']);

        $shipmentRequest->setShipmentDetailsPlt(
            array(
              //  'NumberOfPieces'    => count($this->data['packages']),
                'Pieces'            => $this->data['packages'],
              //  'Weight'            => $totalWeight,
                'WeightUnit'        => $weightUnit == 'kg' ? 'K' : 'L',
                'GlobalProductCode' => $this->data['options']['global_product_code'],
                'LocalProductCode'  => $this->data['options']['local_product_code'],
                'Date'              => date('Y-m-d'),
                'Contents'          => $this->data['options']['contents'],
                'DimensionUnit'     => $dimensionUnit == 'cm' ? 'C' : 'I',
                'PackageType'       => 'EE',
                'CurrencyCode'      => $this->data['options']['insured_value_currency'],
            )
        );
     
        $labelType = Configuration::get('DHL_LABEL_TYPE');
        $labelImageFormat = isset($this->labelFormat[$labelType]) ? $this->labelFormat[$labelType] : $this->labelFormat['pdfa4'];
        $plt_type_selected = Tools::getValue('dhl_plt_service');
      /*  if ($plt_type_selected == "create_invoice" && Tools::getValue('base64_decode') != null) {
            $shipmentRequest->setDocImages(
                array(
                'type'          => "INV",
                'image'         => Tools::getValue('base64_decode'),
                'image_format'  => "PDF",
                )
            );
        }*/
        if ($plt_type_selected == "upload_invoice" && Tools::getValue("pdf_name_submitted") != null) {
            $pdf_name_2_choice = Tools::getValue("pdf_name_submitted");
            $pdf_second_choice_path = _PS_MODULE_DIR_ . 'dhlexpress' . '/pdf/' . $pdf_name_2_choice;
            $pdf_all_content = base64_encode(Tools::file_get_contents($pdf_second_choice_path));
            $shipmentRequest->setDocImages(
                array(
                'type'          => "INV",
                'image'         => $pdf_all_content,
                'image_format'  => "PDF",
                )
            );
        }
        $shipmentRequest->setLabelImageFormatPLT($labelImageFormat);
        $shipmentRequest->setReferenceID($this->data['options']['reference']);
        $client = new DhlClient((int) Configuration::get('DHL_LIVE_MODE'));
        $client->setRequest($shipmentRequest);
        $this->logger->logXmlRequest($shipmentRequest);
        $response = $client->request();
        if ($response && $response instanceof DhlShipmentValidationResponse) {
            $errors = $response->getErrors();
            $this->logger->info('Response received.', array('label_resp' => $response));
            if (empty($errors)) {
                $labelDetails = $response->getLabelDetails();
                if ($this->data['form']['id_dhl_order'] && !$this->data['form']['free_label']) {
                    $idDhlService = DhlService::getIdByProductCode(
                        $labelDetails['GlobalProductCode'],
                        $this->data['options']['doc']
                    );
                    $dhlOrder = new DhlOrder((int) $this->data['form']['id_dhl_order']);
                    $dhlLabel = new DhlLabel();
                    $serviceArea = $labelDetails['ServiceAreaCode'];
                    $countryName = $labelDetails['CountryName'];
                    $chargeableWeight = $labelDetails['ChargeableWeight'];
                    $dhlLabel->id_dhl_order = (int) $dhlOrder->id_dhl_order;
                    $dhlLabel->id_dhl_service = (int) $idDhlService;
                    $dhlLabel->awb_number = pSQL($labelDetails['AirwayBillNumber']);
                    $dhlLabel->return_label = (int) $this->data['form']['id_return_label'];
                    $dhlLabel->label_string = pSQL($labelDetails['LabelImage']['OutputImage']);
                    $dhlLabel->piece_contents = pSQL($labelDetails['Contents']);
                    $dhlLabel->total_pieces = (int) $labelDetails['Piece'];
                    $dhlLabel->total_weight = Tools::strtoupper((float) $chargeableWeight.$weightUnit);
                    $dhlLabel->consignee_contact = pSQL($labelDetails['PersonName']);
                    $dhlLabel->consignee_destination = pSQL($serviceArea.' / '.Tools::strtoupper($countryName));
                    $order = new Order((int) $this->data['form']['id_order']);
                    $dhlLabel->order_reference = $order->reference;
//                    $labelDetails['AirwayBillNumber'] = $order->reference;
                    if (!$dhlLabel->save()) {
                        $this->logger->error('Cannot save label to DB');
                        throw new DhlException($this->module->l('Cannot save label locally', 'AdminDhlLabel'));
                    } else {
                        $this->logger->info('Saving label to DB', array('label' => $dhlLabel));
                        // Order changes to "Handling of shipment in progress" if has not in the past
                        $idDhlOsPreparation = (int) Configuration::get('DHL_OS_PREPARATION');
                        $order = new Order((int) $this->data['form']['id_order']);
                        $orderHistory = $order->getHistory($order->id_lang, (int) $idDhlOsPreparation);
                        if (empty($orderHistory)) {
                            $history = new OrderHistory();
                            $history->id_order = (int) $order->id;
                            $history->changeIdOrderState($idDhlOsPreparation, (int) $order->id);
                            $history->addWithemail();
                            $this->logger->info('Sending mail "Handling shipment in progress"');
                        }
                        $subject = $this->module->l('Handling of shipment in progress', 'AdminDhlLabel');
                        DhlTools::sendHandlingShipmentMail($order, $subject, $dhlLabel->awb_number);
                        $country_code_customer = $this->data['customer']['country'];
                        $country_code_sender   = $this->data["sender"]->iso_country;
                        $zip_code_sender   = $this->data["sender"]->zipcode;
                        $service_plt = $this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender);
                        $this->logger->info('Displaying label details.');
                        $return = array(
                            'errors'           => false,
                            'labelDetails'     => $labelDetails,
                            'plt'              => $service_plt,
                            'id_dhl_label'     => $dhlLabel->id,
                            'alreadyGenerated' => false,
                            'freeLabel'        => false,
                        );
                        $dhlCI = DhlCommercialInvoice::getByIdDhlLabel($dhlLabel->id);
                        if (Validate::isLoadedObject($dhlCI)) {
                            $this->ajaxDie(Tools::jsonEncode($return));
                        } else {
                            $dhlCI = new DhlCommercialInvoice();
                        }
                        $dhlCI->moduleName = $this->module->name;
                        $dhlCI->id_dhl_label = $dhlLabel->id;
                        $dhlCI->id_dhl_order = $dhlOrder->id;
                        $file =  Tools::getValue('file_path');
                        if ($file) {
                            unlink($file);
                        }
                        if ($plt_type_selected == "create_invoice" && Tools::getValue('base64_decode') != null) {
                            $dhlCI->pdf_string = $dhlCI->updateInvoiceInPageLabel(
                                $dhlLabel->awb_number,
                                $this->context->language->id,
                                $this->module->name,
                                $this->context->smarty
                            );
                        }
                        if ($plt_type_selected == "upload_invoice" && Tools::getValue("pdf_name_submitted") != null) {
                            $pdf_name_2_choice = Tools::getValue("pdf_name_submitted");
                            $pdf_second_choice_path = _PS_MODULE_DIR_ . 'dhlexpress' . '/pdf/' . $pdf_name_2_choice;
                            $pdf_all_content = base64_encode(Tools::file_get_contents($pdf_second_choice_path));
                            $dhlCI->pdf_string =  $pdf_all_content;
                        }
                        $dhlCI->save();
                        return $return;
                    }
                } elseif ($this->data['form']['free_label']) {
                    $this->logger->info('This is a free label. Displaying label details.');
                    $return = array(
                        'errors'           => false,
                        'labelDetails'     => $labelDetails,
                        'alreadyGenerated' => false,
                        'freeLabel'        => true,
                    );

                    return $return;
                } else {
                    $this->logger->error('Unexpected error.');
                    throw new DhlException($this->module->l('Unexpected error. Please try again.', 'AdminDhlLabel'));
                }
            } else {
                $this->logger->error('Errors found. Please review response.', array('errors' => $errors));
                throw new DhlException(str_replace('\\n', ' ', $errors['code'].' - '.$errors['text']));
            }
        } else {
            $this->logger->error('Cannot connect to DHL API.');
            throw new DhlException($this->module->l('Cannot connect to DHL API.', 'AdminDhlLabel'));
        }
    }

    /**
     * @param $response
     * @throws DhlException
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function handleDhlServicesResult($response)
    {
        if ($response && $response instanceof DhlQuoteResponse) {
            $errors = $response->getErrors();
            $this->logger->info('Response received.', array('quotation_resp' => $response));
            if (empty($errors)) {
                $services = $response->getServiceDetails();
                if (!$services || empty($services) || !$services['currency']) {
                    $this->logger->error('No product available.');
                    // @formatter:off
                    throw new DhlException(
                        $this->module->l('No product available. Please try again or update your shipment details', 'AdminDhlLabel')
                    );
                    // @formatter:on
                } else {
                    $this->logger->info('Services found.', array('services' => $services));
                    $isoOrderCurrency = $this->data['options']['iso_currency'];
                    $isoQuoteCurrency = $services['currency'];
                    $services['services'] = $this->sortServicesByPrice($services['services']);
                    $return_label = (bool) $this->data['form']["id_return_label"];
                    $freeLabel = (bool) $this->data['form']['free_label'];
                    $serviceWantedCode = '';
                    $dhlServices = DhlService::getServices($this->context->language->id, false);
                    if (!$freeLabel) {
                        $idOrder = $this->data['form']['id_order'];
                        $order = new Order((int) $idOrder);
                        $orderCurency = new Currency((int) $order->id_currency);
                        $dhlOrder = DhlOrder::getByIdOrder((int) $order->id);
                        $dhlService = new DhlService((int) $dhlOrder->id_dhl_service);
                        $shippingCostPaid = number_format($order->total_shipping_tax_incl, 2);
                        $serviceWantedCode = $this->getServiceWantedCode($dhlService, $services['services']);
                        $this->context->smarty->assign(
                            array(
                                'service_wanted' => $dhlService->global_product_name,
                                'shipping_paid'  => $orderCurency->iso_code.' '.$shippingCostPaid,
                            )
                        );
                        if ($isoOrderCurrency !== $isoQuoteCurrency && $idOrder) {
                            $quoteCurrency = new Currency((int) Currency::getIdByIsoCode($isoQuoteCurrency));
                            $orderCurrency = new Currency((int) Currency::getIdByIsoCode($isoOrderCurrency));
                            $conversionRate = $quoteCurrency->conversion_rate / $orderCurrency->conversion_rate;
                            $this->context->smarty->assign(
                                array(
                                    'convert_price' => number_format(
                                        ($shippingCostPaid * $conversionRate * 100) / 100,
                                        2
                                    ),
                                )
                            );
                        }
                    }
                    $dhlServicesList = array();
                    foreach ($dhlServices as $service) {
                        $dhlServicesList[$service['global_product_code']] = 1;
                    }
                    $country_code_customer = $this->data['customer']['country'];
                    $country_code_sender   = $this->data["sender"]->iso_country;
                    $zip_code_sender   = $this->data["sender"]->zipcode;
                    $service_plt = $this->enablePltService($country_code_customer, $country_code_sender, $zip_code_sender);
                    $this->logger->info('Returning services.');
                    $this->context->smarty->assign(
                        array(
                            'dhl_img_path'        => $this->module->getPathUri().'views/img/',
                            'errors'              => false,
                            'free_label'          => $freeLabel,
                            'services'            => $services['services'],
                            'service_wanted_code' => $serviceWantedCode,
                            'services_currency'   => $services['currency'],
                            'available_services'  => $dhlServicesList,
                            'service_plt'         => $service_plt,
                            'link'                => $this->context->link,
                            'return_label'        => $return_label,
                        )
                    );
                    $html = $this->createTemplate('_partials/dhl-services-result.tpl')->fetch();
                    $return = array(
                        'html'   => $html,
                        'errors' => false,
                    );
                    $this->ajaxDie(Tools::jsonEncode($return));
                }
            } else {
                $this->logger->error('Errors found. Please review response.', array('errors' => $errors));
                throw new DhlException(str_replace('\\n', ' ', $errors['code'].' - '.$errors['text']));
            }
        } else {
            $this->logger->error('Cannot connect to DHL API.');
            throw new DhlException($this->module->l('Cannot connect to DHL API.', 'AdminDhlLabel'));
        }
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws SmartyException
     */
    public function ajaxProcessRetrieveDhlService()
    {
        require_once(dirname(__FILE__).'/../../api/loader.php');
        $this->logger->info('Retrieving DHL Services');
        $this->buildFormData();
        try {
            $this->validateRequest();
        } catch (DhlException $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => $e->getErrors(),
                )
            );
            $html = $this->createTemplate('_partials/dhl-services-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $credentials = DhlTools::getCredentials(true);
        $quoteRequest = new DhlQuoteRequest($credentials);
        $quoteRequest->setMetaDataVersion(sprintf('PS%s', _PS_VERSION_));
        if ($this->data['form']['id_return_label']) {
            $this->logger->info('Quotation for a return label.');
            $accountNumber =
                $this->data['sender']->getReturnShippingAccountNumber((int) $this->data['customer']['id_country']);
            $quoteRequest->setReceiver(
                array(
                    'CountryCode' => $this->data['sender']->iso_country,
                    'Postalcode'  => $this->data['sender']->zipcode,
                    'City'        => $this->data['sender']->city,
                )
            );
            $quoteRequest->setSender(
                array(
                    'CountryCode' => $this->data['customer']['country'],
                    'Postalcode'  => $this->data['customer']['zipcode'],
                    'City'        => $this->data['customer']['city'],
                )
            );
        } else {
            $this->logger->info('Quotation is for a standard label.');
            $accountNumber = $this->data['sender']->getAccountNumber();
            $quoteRequest->setSender(
                array(
                    'CountryCode' => $this->data['sender']->iso_country,
                    'Postalcode'  => $this->data['sender']->zipcode,
                    'City'        => $this->data['sender']->city,
                )
            );
            $quoteRequest->setReceiver(
                array(
                    'CountryCode' => $this->data['customer']['country'],
                    'Postalcode'  => $this->data['customer']['zipcode'],
                    'City'        => $this->data['customer']['city'],
                )
            );
        }
        if ($this->data['options']['doc']) {
            $this->logger->info('"doc" shipment.');
            if (array_key_exists('IB', $this->data['doc_extracharges'])) {
                $quoteRequest->setQtdShp(array_keys($this->data['doc_extracharges']));
            }
        } else {
            $this->logger->info('Not a "doc" shipment.');
            $isoFrom = $this->data['sender']->iso_country;
            $isoTo = $this->data['customer']['country'];
            $pcTo = $this->data['customer']['zipcode'];
            if (DhlTools::isDeclaredValueRequired($isoFrom, $isoTo, $pcTo)) {
                $quoteRequest->setIsDutiable('Y');
            } else {
                $quoteRequest->setIsDutiable('N');
            }
            if ($this->data['options']['declared_value']) {
                $quoteRequest->setDuty(
                    array(
                        'DeclaredValue'    => $this->data['options']['declared_value'],
                        'DeclaredCurrency' => $this->data['options']['declared_value_currency'],
                    )
                );
            }
            if (array_key_exists('II', $this->data['extracharges'])) {
                $quoteRequest->setInsurance(
                    array(
                        'InsuredValue'    => $this->data['options']['insured_value'],
                        'InsuredCurrency' => $this->data['options']['insured_value_currency'],
                    )
                );
            }
            $quoteRequest->setQtdShp(array_keys($this->data['extracharges']));
        }
        $quoteRequest->setPackageDetails(
            array(
                'PaymentCountryCode'   => DhlTools::getIsoCountryById(
                    (int) Configuration::get(
                        'DHL_ACCOUNT_OWNER_COUNTRY'
                    )
                ),
                'Date'                 => date('Y-m-d'),
                'ReadyTime'            => 'PT'.date('H').'H'.date('i').'M',
                'DimensionUnit'        => Tools::strtoupper(DhlTools::getDimensionUnit()),
                'WeightUnit'           => Tools::strtoupper(DhlTools::getWeightUnit()),
                'Pieces'               => $this->data['packages'],
                'PaymentAccountNumber' => $accountNumber,
            )
        );
        $client = new DhlClient(1);
        $client->setRequest($quoteRequest);
        $this->logger->logXmlRequest($quoteRequest);
        $response = $client->request();
        try {
            $this->handleDhlServicesResult($response);
        } catch (DhlException $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => array($e->getErrors()),
                )
            );
            $html = $this->createTemplate('_partials/dhl-services-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
    }

    /**
     *
     */
    public function ajaxProcessValidateBulkLabelForm()
    {
        $errors = array();
        $idPackage = Tools::getValue('dhl_package_type');
        $declaredValue = Tools::getValue('dhl_use_declared_value') ? 1 : Tools::getValue('dhl_declared_value');
        if (!Tools::getValue('dhl_use_order_weight') && (float) !Tools::getValue('dhl_package_weight_'.$idPackage)) {
            // @formatter:off
            $errors = array(
                'description' => $this->module->l('You chose to use package weight. Therefore you need to fill a valid weight value.', 'AdminDhlLabel'),
                'errors'      => true,
            );
            // @formatter:on
        }
        if (Tools::getValue('dhl_insure_shipment') &&
            ((int) !Tools::getValue('dhl_insured_value') || (int) !$declaredValue)
        ) {
            // @formatter:off
            $errors = array(
                'description' => $this->module->l('You chose to insure shipments. Therefore you need to fill both insured value and declared value.', 'AdminDhlLabel'),
                'errors'      => true,
            );
            // @formatter:on
        }
        if (!Tools::getValue('dhl_contents')) {
            $errors = array(
                'description' => $this->module->l('You must fill a shipment content.', 'AdminDhlLabel'),
                'errors'      => true,
            );
        }
        if (!empty($errors)) {
            $this->ajaxDie(Tools::jsonEncode($errors));
        }
        $return = array(
            'errors' => false,
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessGenerateBulkLabel()
    {
        $this->buildBulkShipmentData();
        try {
            $this->buildProductsData();
            $this->validateRequest();
            $return = $this->generateLabel();
        } catch (DhlException $e) {
            $return = array(
                'description' => $e->getErrors(),
                'errors'      => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        } catch (Exception $e) {
            $return = array(
                'description' => $e->getMessage(),
                'errors'      => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessGenerateFormLabel()
    {
        $this->buildFormData();
        try {
            $this->buildProductsData();
            $this->validateRequest();
            $return = $this->generateLabel();
        } catch (DhlException $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => $e->getErrors(),
                )
            );
            $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        } catch (Exception $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => $e->getMessage(),
                )
            );
            $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $this->context->smarty->assign(
            $return + array(
                'dhl_img_path' => $this->module->getPathUri().'views/img/',
                'link'         => $this->context->link,
            )
        );
        $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
                
        $return = array(
            'html'   => $html,
            'errors' => false,
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

     /**
     * @throws Exception
     * @throws SmartyException
     */
    public function ajaxProcessGenerateFormLabelAndSaveInvoice()
    {
        $this->buildFormData();
        try {
            $this->buildProductsData();
            $this->validateRequest();
            $return = $this->generateLabelAndInvoice();
        } catch (DhlException $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => $e->getErrors(),
                )
            );
            $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        } catch (Exception $e) {
            $this->context->smarty->assign(
                array(
                    'errors'      => true,
                    'description' => $e->getMessage(),
                )
            );
            $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
            $return = array(
                'html'   => $html,
                'errors' => true,
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $this->context->smarty->assign(
            $return + array(
                'dhl_img_path' => $this->module->getPathUri().'views/img/',
                'link'         => $this->context->link,
            )
        );
        $html = $this->createTemplate('_partials/dhl-label-result.tpl')->fetch();
                
        $return = array(
            'html'   => $html,
            'errors' => false,
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }


    /**
     *
     */
    public function ajaxProcessAddDhlPackage()
    {
        $idDhlPackage = (int) Tools::getValue('id_dhl_package');
        $dhlPackage = new DhlPackage($idDhlPackage);
        $this->logger->info('Adding package #'.(int) $idDhlPackage.' to the shipment.');
        if ($dhlPackage && Validate::isLoadedObject($dhlPackage)) {
            $return = array(
                'errors'         => false,
                'packageDetails' => array(
                    'id'     => (int) $dhlPackage->id,
                    'name'   => Tools::safeOutput($dhlPackage->name),
                    'width'  => (int) Tools::getValue('width') ? (int) Tools::getValue('width') : 1,
                    'length' => (int) Tools::getValue('length') ? (int) Tools::getValue('length') : 1,
                    'depth'  => (int) Tools::getValue('depth') ? (int) Tools::getValue('depth') : 1,
                    'weight' => (float) Tools::getValue('weight'),
                ),
                'init'           => array(
                    'width'  => (float) $dhlPackage->width_value,
                    'length' => (float) $dhlPackage->length_value,
                    'depth'  => (float) $dhlPackage->depth_value,
                    'weight' => (float) $dhlPackage->weight_value,
                ),
            );
            $this->logger->info('Package details.', array('details' => $return));
            $this->ajaxDie(Tools::jsonEncode($return));
        }
    }

    /**
     *
     */
    public function ajaxProcessLoadAddresses()
    {
        $idCustomer = (int) Tools::getValue('idCustomer');
        $customer = new Customer((int) $idCustomer);
        $this->logger->info('Loading customer #'.(int) $idCustomer.' addresses.');
        if (!Validate::isLoadedObject($customer)) {
            $this->logger->error('Cannot load customer address list.');
            $return = array(
                'errors'      => true,
                'noAddresses' => true,
                'description' => $this->module->l('Cannot load customer addresses.', 'AdminDhlLabel'),
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $addresses = $customer->getAddresses($this->context->language->id);
        if (empty($addresses)) {
            $this->logger->info('Customer does not have addresses yet.');
            $return = array(
                'errors'      => true,
                'noAddresses' => true,
                'description' => $this->module->l('Customer does not have any addresses.', 'AdminDhlLabel'),
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $validAddresses = array();
        foreach ($addresses as $address) {
            $address['id'] = $address['id_address'];
            $validAddresses[] = $this->formatCustomerAddress($address, $customer->email);
        }
        $this->logger->info('Returning valid address list.', array('addresses' => $validAddresses));
        $return = array(
            'errors'    => false,
            'addresses' => $validAddresses,
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    /**
     *
     */
    public function ajaxProcessLoadAddress()
    {
        $idAddress = (int) Tools::getValue('idAddress');
        $address = new Address((int) $idAddress);
        $this->logger->info('Loading address #'.(int) $idAddress);
        if (!Validate::isLoadedObject($address)) {
            $this->logger->error('Address is not valid');
            // @formatter:off
            $return = array(
                'errors'      => true,
                'noAddresses' => true,
                'description' => $this->module->l('Address not valid, please fill the customer address manually.', 'AdminDhlLabel'),
            );
            // @formatter:on
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $customer = new Customer((int) $address->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            $this->logger->error('Cannot load address. Merchant will fill it manually.');
            // @formatter:off
            $return = array(
                'errors'      => true,
                'noAddresses' => false,
                'description' => $this->module->l('Cannot load address, please fill the customer address manually.', 'AdminDhlLabel'),
            );
            // @formatter:on
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $this->logger->info('Returning address', array('address' => $address));
        $return = array(
            'errors'  => false,
            'address' => $this->formatCustomerAddress((array) $address, $customer->email),
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    /**
     * @throws Exception
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws SmartyException
     */
    public function ajaxProcessDeleteLabel()
    {
        /** @var Dhlexpress $module */
        $module = $this->module;
        $idDhlLabel = Tools::getValue('id_dhl_label');
        $this->logger->info('Deleting label #'.(int) $idDhlLabel);
        $dhlLabel = new DhlLabel((int) $idDhlLabel);
        if (!Validate::isLoadedObject($dhlLabel)) {
            $this->logger->error('Label not valid.');
            $this->ajaxDie(
                Tools::jsonEncode(
                    array(
                        'errors'  => true,
                        'message' => $this->module->l('Label not valid.', 'AdminDhlLabel'),
                    )
                )
            );
        }
        $dhlOrder = new DhlOrder((int) $dhlLabel->id_dhl_order);
        if (!Validate::isLoadedObject($dhlOrder)) {
            $this->logger->error(
                'DHL Order not valid.',
                array(
                    'id' => (int) $dhlLabel->id_dhl_order,
                )
            );
            $this->ajaxDie(
                Tools::jsonEncode(
                    array(
                        'errors'  => true,
                        'message' => $this->module->l('Order not valid.', 'AdminDhlLabel'),
                    )
                )
            );
        }
        if ($dhlLabel->return_label) {
            $this->logger->info('Label is a return label.');
            $delete = $dhlLabel->delete();
        } else {
            $dhlCI = DhlCommercialInvoice::getByIdDhlLabel((int) $dhlLabel->id);
            $dhlReturnLabel = new DhlLabel($dhlLabel->return_label);
            $this->logger->info(
                'Label is a standard label',
                array(
                    'dhl_commercial'   => $dhlCI,
                    'dhl_return_label' => $dhlReturnLabel,
                )
            );
            $delete = $dhlLabel->deleteLabel($dhlCI, $dhlReturnLabel);
        }
        if (!$delete) {
            $this->logger->error('Could not delete label.');
            Db::getInstance()->delete('shipment_tracking', 'id_dhl_label = '.(int) $idDhlLabel);
            $this->ajaxDie(
                Tools::jsonEncode(
                    array(
                        'errors'  => true,
                        'message' => $this->module->l('Cannot delete label.', 'AdminDhlLabel'),
                    )
                )
            );
        }
        $this->logger->info('Label deleted successfully.');
        $htmlTable = $module->getDhlShipmentDetailsTable($dhlOrder, false);
        $this->ajaxDie(
            Tools::jsonEncode(
                array(
                    'errors'  => false,
                    'message' => $this->module->l('Label deleted successfully', 'AdminDhlLabel'),
                    'html'    => $htmlTable,
                )
            )
        );
    }

    /**
     * @throws PrestaShopDatabaseException
     */
    public function ajaxProcessSearchCustomers()
    {
        $searches = explode(' ', Tools::getValue('customer_search'));
        $customers = array();
        $searches = array_unique($searches);
        foreach ($searches as $search) {
            if (!empty($search) && $results = Customer::searchByName($search, 50)) {
                foreach ($results as $result) {
                    if ($result['active']) {
                        $result['fullname_and_email'] =
                            $result['firstname'].' '.$result['lastname'].' - '.$result['email'];
                        $customers[$result['id_customer']] = $result;
                    }
                }
            }
        }

        if (count($customers) && Tools::getValue('sf2')) {
            $to_return = $customers;
        } elseif (count($customers) && !Tools::getValue('sf2')) {
            $to_return = array(
                'customers' => $customers,
                'found'     => true,
            );
        } else {
            $to_return = Tools::getValue('sf2') ? array() : array('found' => false);
        }

        $this->ajaxDie(Tools::jsonEncode($to_return));
    }
 
    
    public function enablePltService($country_code_customer, $country_code_sender, $zip_code_sender)
    {
        $destinationType = DhlTools::getDestinationType($country_code_customer, $country_code_sender, $zip_code_sender);
        if ($destinationType == 'WORLDWIDE') {
            $unable_outbound = $this->getOutbound($country_code_sender);
            if ($unable_outbound == 0) {
                return self::PLT_AVAILABLE_NOT_ELIGIBLE;
            } else {
                $unable_inbound = $this->getInbound($country_code_customer);
                if ($unable_inbound == 0) {
                    return self::PLT_AVAILABLE_NOT_ELIGIBLE;
                } else {
                    $conversion_rate = Dhlexpress::getConversionRateFromBdd();
                    $amount = $this->getAmountFromBdd($country_code_sender);
                    $total_paied = Tools::getValue('dhl_label_declared_value');
                    if ($amount != 0) {
                        $amount = $amount * 0.95;
                        $amount_limit_converted = $amount / $conversion_rate;
                        if ($amount_limit_converted >= $total_paied) {
                            $amount_in = $this->getAmountFromBdd($country_code_customer);
                            if ($amount_in != 0) {
                                $amount_in = $amount_in * 0.95;
                                $amount_limit_converted_in = $amount_in / $conversion_rate;
                                if ($amount_limit_converted_in >= $total_paied) {
                                    return self::PLT_AVAILABLE;
                                } else {
                                    return self::PLT_AVAILABLE_NOT_ELIGIBLE;
                                }
                            } else {
                                return self::PLT_AVAILABLE;
                            }
                        } else {
                            return self::PLT_AVAILABLE_NOT_ELIGIBLE;
                        }
                    } else {
                        $amount_in = $this->getAmountFromBdd($country_code_customer);
                        if ($amount_in != 0) {
                            $amount_in = $amount_in * 0.95;
                            $amount_limit_converted_in = $amount_in / $conversion_rate;
                            if ($amount_limit_converted_in >= $total_paied) {
                                return self::PLT_AVAILABLE;
                            } else {
                                return self::PLT_AVAILABLE_NOT_ELIGIBLE;
                            }
                        } else {
                            return self::PLT_AVAILABLE;
                        }
                    }
                }
            }
        } else {
            return self::PLT_NOT_AVAILABLE;
        }
    }
        
    public function getOutbound($country_code_sender)
    {
        $sql = "SELECT `outbound` FROM `". _DB_PREFIX_ ."dhl_plt` WHERE `country_code` = '$country_code_sender'";
        $result = Db::getInstance()->executeS($sql);
        if ($result == null || $result == '' || $result == 0) {
            return 0;
        } else {
            return $result[0]["outbound"];
        }
    }
    public function getInbound($country_code_customer)
    {
        $sql = "SELECT `inbound` FROM `". _DB_PREFIX_ ."dhl_plt` WHERE `country_code` = '$country_code_customer'";
        $result = Db::getInstance()->executeS($sql);
        if ($result == null || $result == '' || $result == 0) {
            return 0;
        } else {
            return $result[0]["inbound"];
        }
    }
    public function getAmountFromBdd($country_code_sender)
    {
        $sql = "SELECT `amount` FROM `". _DB_PREFIX_ ."dhl_plt` WHERE `country_code` = '$country_code_sender'";
        $result = Db::getInstance()->executeS($sql);
        if ($result == null || $result == '' || $result == 0) {
            return 0;
        } else {
            return $result[0]["amount"];
        }
    }
    
    public function worldwideDestination()
    {
        $destinationType = DhlTools::getDestinationType(
            $this->data['customer']['country'],
            $this->data['sender']->iso_country,
            $this->data['sender']->zipcode 
        );
        if ($destinationType == 'WORLDWIDE') {
            $this->logger->info('Set archive doc to "Y" (worldwide shipment)');
        } else {
            $this->logger->info('Set archive doc to "Y" (worldwide shipment)');
        }
    }
    
    public function assingParamsInvoice($order)
    {
        $orderCurrencyIso = $order->id_currency;
        $currencyIso = new Currency((int) $orderCurrencyIso);
        $customerAddrDelivery = new Address((int) $order->id_address_delivery);
        $senderAddresses = DhlAddress::getAddressList();
        $updateDhlAddrLink = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->module->name;
        $updateAddrLink = $this->context->link->getAdminLink('AdminAddresses');
        $updateAddrLink .= '&updateaddress&id_address='.$customerAddrDelivery->id;
        $defaultSenderAddrDelivery = (int) Configuration::get('DHL_DEFAULT_SENDER_ADDRESS');
        $incoterms = array(
            'DAP' => $this->module->l('Delivered At Place', 'AdminDhlCommercialInvoice'),
            'DDP' => $this->module->l('Delivered Duty Paid', 'AdminDhlCommercialInvoice'),
        );
        $exportationType = array(
            'P' => $this->module->l('Permanent', 'AdminDhlCommercialInvoice'),
            'T' => $this->module->l('Temporary', 'AdminDhlCommercialInvoice'),
            'R' => $this->module->l('Repair and Return', 'AdminDhlCommercialInvoice'),
        );
        $countries = Country::getCountries($this->context->language->id);
        $defaultCountry = Country::getByIso('FR');
        $orderDetails = OrderDetail::getList((int) $order->id);
        $this->context->smarty->assign(
            array(
                'smarty'                 => $this->context->smarty,
                'link'                   => $this->context->link,
                'currency_iso'           => $currencyIso->iso_code,
                'weight_unit'            => DhlTools::getWeightUnit(),
                'sender_addresses'       => $senderAddresses,
                'default_sender_address' => $defaultSenderAddrDelivery,
                'update_dhl_addr_link'   => $updateDhlAddrLink,
                'update_addr_link'       => $updateAddrLink,
                'customer_address'       => $customerAddrDelivery,
                'customer_country_iso'   => DhlTools::getIsoCountryById((int) $customerAddrDelivery->id_country),
                'incoterms'              => $incoterms,
                'exportation_type'       => $exportationType,
                'default_hs_code'        => Configuration::get('DHL_DEFAULT_HS_CODE'),
                'countries'              => $countries,
                'default_country'        => $defaultCountry,
                'order_details'          => $orderDetails,
                'id_order'               => (int) $order->id,
                'awb_number'             => $order->reference,
            )
        );
    }
    public function postProcessUploadPdfInvoice()
    {
        
        if (!isset($_FILES['eg_pdf_invoice']['error']) || is_array($_FILES['eg_pdf_invoice']['error'])) {
            die($this->l('Invalid parameters.'));
        }
        switch ($_FILES['eg_pdf_invoice']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                die($this->l('No files sent.'));
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                die($this->l('Exceeded filesize limit.'));
            default:
                die($this->l('Unknown errors.'));
        }
        $fileType = $_FILES['eg_pdf_invoice']['type'];
        if ($fileType != 'application/pdf') {
            die($this->l('You must submit only pdf files. '));
        }
        //$filenameArray = explode('.', $_FILES['eg_pdf_invoice']['name']);
        $current_date = date("Y_m_d_H_i_s");
        $filename = $current_date .'DHL_Pdf_CommercialInvoice.pdf';
        $file = _PS_MODULE_DIR_ . 'dhlexpress' . '/pdf/' . $filename;
        if (move_uploaded_file($_FILES['eg_pdf_invoice']['tmp_name'], $file)) {
            die($filename) ;
        } else {
            die($this->l('Cannot upload image file.'));
        }
    }

    public function ajaxProcessDeleteInvoice()
    {
        $file = Tools::getValue('pdf_name_submitted');
        $path_file = _PS_MODULE_DIR_ . 'dhlexpress' . '/pdf/' . $file;
        if (file_exists($path_file)) {
            unlink($path_file);
        }
    }
    
    public function diplayNbrPackagesInLabel()
    {
        $labelRegText = '';
        $nbr_DG_activated = sizeof($this->data['extracharges']);
        $i=0;
        foreach ($this->data['extracharges'] as $key => $value) {
            $i= $i+1;
            if ($key == 'HE') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-3');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HH') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-4');
                $code_UN = Tools::getValue('TYPE_DESIGNATION_UN_XXXX');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value .' '. $code_UN. ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value .' '. $code_UN. ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HB') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-5');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HD') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-6');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HV') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-7');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HM') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-8');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HW') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-9');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
            
            if ($key == 'HK') {
                $nbr_packages = Tools::getValue('dhl-number-pieces-concerned-11');
                if (($nbr_DG_activated == 1) || ($nbr_DG_activated == $i)) {
                    $labelRegText .= $value . ' x' .$nbr_packages;
                } else {
                    $labelRegText .= $value . ' x' .$nbr_packages.'  -  ';
                }
            }
        }
        return $labelRegText;
    }
}
