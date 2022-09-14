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
 * Class DhlCommercialInvoice
 */
class DhlCommercialInvoice extends ObjectModel
{
    /** @var int $idOrder */
    public $idOrder;

    /** @var string $moduleName */
    public $moduleName;

    /** @var array $productVars */
    public $productVars;

    /** @var array $invoiceVars */
    public $invoiceVars;

    /** @var int $id_dhl_commercial_invoice */
    public $id_dhl_commercial_invoice;

    /** @var int $id_dhl_order */
    public $id_dhl_order;

    /** @var int $id_dhl_label */
    public $id_dhl_label;

    /** @var int $pdf_string */
    public $pdf_string;

    /** @var string $date_add */
    public $date_add;

    /** @var array $definition */
    public static $definition = array(
        'table'   => 'dhl_commercial_invoice',
        'primary' => 'id_dhl_commercial_invoice',
        'fields'  => array(
            'id_dhl_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_dhl_label' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'size' => 80),
            'pdf_string'   => array('type' => self::TYPE_STRING, 'validate' => 'isAnything'),
            'date_add'     => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * @param int $idDhlLabel
     * @return bool|DhlCommercialInvoice
     */
    public static function getByIdDhlLabel($idDhlLabel)
    {
        $dbQuery = new DbQuery();
        $dbQuery->select('dci.'.self::$definition['primary']);
        $dbQuery->from(self::$definition['table'], 'dci');
        $dbQuery->where('dci.id_dhl_label = '.(int) $idDhlLabel);
        $id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($dbQuery);
        if (false === $id) {
            return false;
        } else {
            return new self((int) $id);
        }
    }

    public static function updateInvoiceInPageLabel($awb_number, $language, $module_name, $context)
    {
        $dhlCI = new DhlCommercialInvoice();
        $idOrder = Tools::getValue('dhl_id_order');
        $orderDetails = OrderDetail::getList((int) $idOrder);
        $order = new Order((int) $idOrder);
        $currency = new Currency((int) $order->id_currency);
        $productVars = array();
        $totalQuantity = 0;
        $totalDeclaredValue = 0;
        $totalWeight = 0;
        foreach ($orderDetails as $orderDetail) {
            if (Tools::isSubmit('dhl_od_country_'.(int) $orderDetail['id_order_detail'])) {
                $idCountry = Tools::getValue('dhl_od_country_'.(int) $orderDetail['id_order_detail']);
                $unitPrice = Tools::ps_round(Tools::getValue('dhl_od_price_'.(int) $orderDetail['id_order_detail']), 2);
                $qty = Tools::getValue('dhl_od_quantity_'.(int) $orderDetail['id_order_detail']);
                $weight = Tools::getValue('dhl_od_weight_'.(int) $orderDetail['id_order_detail']);
                $subtotalWeight = (float) $weight * (int) $qty;
                $totalPrice = (int) $qty * (float) $unitPrice;
                $productVars[] = array(
                    'product_name'         => Tools::getValue('dhl_od_pname_'.(int) $orderDetail['id_order_detail']),
                    'product_quantity'     => (int) $qty,
                    'unit_price_tax_excl'  => (float) $unitPrice,
                    'total_price_tax_excl' => (float) $totalPrice,
                    'product_weight'       => (float) $weight,
                    'subtotal_weight'      => (float) $subtotalWeight,
                    'origin'               => Country::getNameById($language, (int) $idCountry),
                    'commodity_code'       => Tools::getValue('dhl_od_hs_code_'.(int) $orderDetail['id_order_detail']),
                );
                $totalQuantity += (int) $qty;
                $totalDeclaredValue += (float) $totalPrice;
                $totalWeight += (float) $subtotalWeight;
            }
        }
        $posts = array_keys(Tools::getAllValues());
        foreach ($posts as $postKey) {
            if (Tools::substr($postKey, 0, 17) == 'dhl_supp_country_') {
                $id = (int) Tools::substr($postKey, 17);
                $country = Tools::getValue('dhl_supp_country_'.(int) $id);
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
                    'subtotal_weight'      => (float) $subtotalWeight,
                    'origin'               => $country,
                    'commodity_code'       => Tools::getValue('dhl_supp_hs_code_'.(int) $id),
                );
                $totalQuantity += (int) $qty;
                $totalDeclaredValue += (float) $totalPrice;
                $totalWeight += (float) $subtotalWeight;
            }
        }
        $dhlCI->moduleName = $module_name;
        $dhlCI->idOrder = $idOrder;
        $signatureName = Configuration::get('DHL_PLT_SIGNATURE');
        $signature = _PS_MODULE_DIR_ .'dhlexpress/views/img/'.$signatureName.'.jpg';
        if (!file_exists($signature)) {
            $signature = "";
        }
        $consigneeAddress = new Address((int) Tools::getValue('dhl_customer_address'));
        $showVatGB = false;
        if(Country::getIsoById((int) $consigneeAddress->id_country) == "GB"){
            $showVatGB = true;
        } 

        $dhlCI->invoiceVars = array(
            'sender_details'    => new DhlAddress((int) Tools::getValue('dhl_sender_address')),
            'consignee_details' => $consigneeAddress,
            'awb_number'        => $awb_number,
            'declared_value'    => (float) $totalDeclaredValue,
            'total_package'     => Tools::getValue('dhl_total_package'),
            'total_weight'      => (float) $totalWeight,
            'incoterms'         => Tools::getValue('dhl_invoice_incoterms'),
            'total_quantity'    => $totalQuantity,
            'weight_unit'       => DhlTools::getWeightUnit(),
            'currency_code'     => $currency->iso_code,
            'signature'         => $signature,
            'order_reference'   => $order->reference,
            'show_vat_gb'      => $showVatGB,
        );
        $dhlCI->productVars = $productVars;
        $pdf = new PDF($dhlCI, 'DhlInvoice', $context);
        $pdf->pdf_renderer = new DhlPDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'));
        $pdf->pdf_renderer->SetMargins(10, 10, 10);
        $pdf->pdf_renderer->SetFooterMargin(15);
        $pdfString = base64_encode($pdf->render(false));
        $dhlCI->pdf_string = $pdfString;
        $file = sys_get_temp_dir();
        if ($file && Tools::substr($file, -1) != DIRECTORY_SEPARATOR) {
            $file .= DIRECTORY_SEPARATOR;
        }
        $current_date = date("Y_m_d_H_i_s");
        $file .= $current_date . 'DHL_CommercialInvoice.pdf';
        file_put_contents($file, base64_decode($pdfString));
        unlink($file);
        return $pdfString;
    }
}
