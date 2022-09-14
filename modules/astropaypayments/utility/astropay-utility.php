<?php
/**
* 2007-2020 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class AstroPayUtility
{
    protected const ERRORS = [
        400 => 'Incorrect request body format.',
        401 => 'Incorrect API-Key',
        404 => 'There is no entry with that reference.',
        412 => 'An entry with this ID already exists.',
        500 => 'Internal error, please contact support.',
    ];

    public const PAYMENT_METHODS = [
        'AB' => 'Agribank',
        'AE' => 'American Express',
        'AL' => 'Airtel Money',
        'AS' => 'Asia Commercial Bank',
        'BB' => 'Banco Brasil',
        'BC' => 'BCP',
        'BD' => 'BIDV Bank',
        'BL' => 'Boleto',
        'BQ' => 'Banorte',
        'BT' => 'Bitcoin',
        'BU' => 'Baloto',
        'BV' => 'Bancomer',
        'BW' => 'Bodega Aurrera',
        'BX' => 'Banco de Chile',
        'BY' => 'CIMB Bank Berhad',
        'CR' => 'Carulla',
        'CU' => 'Circulo K',
        'CZ' => 'Bank Central Asia',
        'DC' => 'Diners',
        'DO' => 'DongA Bank',
        'EF' => 'PagoEfectivo',
        'EL' => 'Elo',
        'EM' => 'Eximbank',
        'EX' => 'Almacenes Exito',
        'EY' => 'Efecty',
        'FA' => 'Farmacias del ahorro',
        'FB' => 'Farmacia Benavides',
        'FC' => 'FreeCharge',
        'FO' => 'Facilito',
        'GO' => 'Google Pay',
        'GS' => 'Government Savings Bank',
        'HC' => 'Caja Huancayo',
        'HO' => 'Hong Leong Bank Berhad',
        'HP' => 'Hipercard',
        'IA' => 'Itau',
        'IB' => 'InterBank',
        'IG' => 'Internet Banking',
        'IR' => 'Interac Online',
        'IX' => 'Pix',
        'JM' => 'Jio Money',
        'KB' => 'Bangkok Bank',
        'KR' => 'Krungsri',
        'KS' => 'Kasikorn Bank',
        'KT' => 'Krung Thai Bank',
        'LC' => 'Loterias Caixa',
        'MB' => 'Mandiri Bank',
        'MC' => 'Mastercard',
        'MJ' => 'Multicaja',
        'MM' => 'Mobile Money',
        'MP' => 'M-Pesa',
        'MY' => 'Maybank Berhad',
        'NB' => 'Net Banking India',
        'NG' => 'Bank Negara Indonesia',
        'OM' => 'Ola Money',
        'OX' => 'OXXO',
        'PC' => 'PSE',
        'PH' => 'PhonePe',
        'PM' => 'Perfect Money',
        'PU' => 'Public Bank Berhad',
        'RH' => 'RHB Banking Group',
        'SA' => 'Siam Commercial Bank',
        'SC' => 'Santander',
        'SE' => 'Spei',
        'SF' => 'Banco Safra',
        'SJ' => 'Banco Sicredi',
        'SK' => 'Sacombank',
        'SS' => 'Sams Club',
        'SU' => 'Superama',
        'SX' => 'Surtimax',
        'TB' => 'Thai Military Bank',
        'TC' => 'ToditoCash',
        'TH' => 'Techcombank',
        'TL' => 'Trustly',
        'TR' => 'Bank Transfer',
        'UD' => 'Ussd',
        'UI' => 'UPI',
        'UL' => 'Banrisul',
        'US' => 'Caja Cusco',
        'VC' => 'Virtual Account - Bank Negara Indonesia',
        'VE' => 'Verve',
        'VI' => 'Visa',
        'VJ' => 'Virtual Account - Mandiri Bank Indonesia',
        'VM' => 'Virtual Account - MayBank Indonesia',
        'VN' => 'Vietin Bank',
        'VT' => 'Vietcombank',
        'WA' => 'Walmart',
        'WP' => 'WebPay',
        'WU' => 'Western Union',
        'ZB' => 'Zimpler Banking',
        'ZP' => 'Zimpler'
    ];

    private static function getBaseUrl()
    {
        return Configuration::get('ASTROPAY_TEST_MODE', false) ?
            'https://onetouch-api-sandbox.astropay.com/' :
            'https://onetouch-api.astropay.com/';
    }

    private static function createHeaders($body = '')
    {
        $api_key = Configuration::get('ASTROPAY_API_KEY', null);
        $secret = Configuration::get('ASTROPAY_SECRET', null);

        if (empty($api_key) || empty($secret)) {
            throw new \Exception('AstroPay Keys have not been configured');
        }

        return [
            'Merchant-Gateway-Api-Key: '. $api_key,
            'Signature: '. hash_hmac('sha256', $body, $secret),
            'Signature-Algorithm: HMACSHA256',
            'Content-Type: application/json'
        ];
    }

    public static function postData($path, $data = [])
    {
        $url = static::getBaseUrl() . $path;

        $body = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($body === null) {
            throw new \Exception('Error while encoding data: ' . json_last_error_msg());
        }

        $headers = static::createHeaders($body);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
        ));

        if (Configuration::get('ASTROPAY_TEST_MODE', false)) {
            PrestaShopLogger::addLog("AstroPay Post to $url: body $body and headers " . var_export($headers, true));
        }

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);

        if (Configuration::get('ASTROPAY_TEST_MODE', false)) {
            PrestaShopLogger::addLog("Received response $response with code $code and error $error");
        }

        curl_close($curl);

        return static::processCurlResponse($response, $code, $error);
    }

    public static function getData($path)
    {
        $url = static::getBaseUrl() . $path;
        $headers = static::createHeaders();

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
        ));

        if (Configuration::get('ASTROPAY_TEST_MODE', false)) {
            PrestaShopLogger::addLog("AstroPay Get from $url with headers " . var_export($headers, true));
        }

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);

        if (Configuration::get('ASTROPAY_TEST_MODE', false)) {
            PrestaShopLogger::addLog("Received response $response with code $code and error $error");
        }

        curl_close($curl);

        return static::processCurlResponse($response, $code, $error);
    }

    private static function processCurlResponse($response, $code, $error)
    {
        if (!empty($error)) {
            throw new \Exception($error);
        }

        $data = is_string($response) ? json_decode($response, true) : $response;
        if (isset($data['error'])) {
            if (isset($data['description'])) {
                throw new \Exception($data['description']);
            }
            $error = $data['error'];
        }

        if ($code >= 400) {
            $error = static::ERRORS[ $code ] ?? ($error ?: "Unknown error with code $code");
        }

        if (!empty($error)) {
            throw new \Exception($error);
        }

        if ($data === null) {
            throw new \Exception(json_last_error());
        }

        return $data;
    }
}
