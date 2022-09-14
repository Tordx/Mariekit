<?php
/**
 * Module Front Controller
 *
 * @author   Twispay
 * @version  1.0.1
 */

class TwispayValidationModuleFrontController extends ModuleFrontController
{
    /** Method that provides mechanisms to process the IPN REQUESTS */
    public function init()
    {
        /** Check if the POST is corrupted: Doesn't contain the 'opensslResult' and the 'result' fields. */
        if (false == Tools::getValue('opensslResult') && false == Tools::getValue('result')) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Received empty response.'));
            die($this->l('[RESPONSE-ERROR]: Received empty response.'));
        }

        /** Check if the api key is defined */
        $keys = $this->module->getKeysInfo();
        if (!$keys) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Private key is not valid.'));
            die($this->l('[RESPONSE-ERROR]: Private key is not valid.'));
        }
        $apiKey = $keys['privateKey'];

        /** Extract the server response and decript it. */
        $decrypted = Twispay_Response::twispay_decrypt_message(/*tw_encryptedResponse*/Tools::getValue('opensslResult') != false ? Tools::getValue('opensslResult') : Tools::getValue('result'), $apiKey);

        /** Check if decryption failed.  */
        if (false === $decrypted) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Decryption failed.'));
            die($this->l('[RESPONSE-ERROR]: Decryption failed.'));
        } else {
            Twispay_Logger::log($this->l('[RESPONSE]: Decrypted string: ').Tools::jsonEncode($decrypted));
        }

        /** Check if order already exist */
        if (Twispay_Transactions::checkTransaction($decrypted['transactionId'])) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Order already validated, transaction id '). $decrypted['transactionId']);
            die("OK");
        }

        /** Validate the decripted response. */
        $orderValidation = Twispay_Response::twispay_checkValidation($decrypted, $this->module);

        /** Check if server response validation failed.  */
        if (true !== $orderValidation) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Validation failed.'));
            die($this->l('[RESPONSE-ERROR]: Validation failed.'));
        }

        /** Fix Kernel error in Prestashop 1.7.6 */
        global $kernel;
        if(!$kernel && file_exists(_PS_ROOT_DIR_.'/app/AppKernel.php')){
            require_once _PS_ROOT_DIR_.'/app/AppKernel.php';
            $kernel = new \AppKernel('prod', false);
            $kernel->boot();
        }
        /** End fix */

        /** Update the transaction status. */
        if(Twispay_Status_Updater::updateStatus_ipn($decrypted, $this)){
          die('OK');
        }else{
          die("Internal processing failure");
        }
    }

    public function l($message)
    {
        return $this->module->l($message);
    }
}
