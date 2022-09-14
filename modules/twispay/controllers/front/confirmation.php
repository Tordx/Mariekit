<?php
/**
 * Module Front Controller
 *
 * @author   Twispay
 * @version  1.0.1
 */

class TwispayConfirmationModuleFrontController extends ModuleFrontController
{
    /** Method that provides mechanisms to process the BACKURL REQUESTS */
    public function postProcess()
    {
        /* Check if the POST is corrupted: Doesn't contain the 'opensslResult' and the 'result' fields. */
        /* OR */
        /* Check if the POST is corrupted: Doesn't contain the 'secure_key' field. */
        /* OR */
        /* Check if the POST is corrupted: Doesn't contain the 'cart_id' field. */
        if ((false == Tools::getValue('opensslResult') && false == Tools::getValue('result')) || (false == Tools::getValue('secure_key')) || (false == Tools::getValue('cart_id'))) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Received empty response.'));
            return $this->showNotice();
        }

        /** Check if the api key is defined */
        $module_id = $this->module->id;
        $keys = $this->module->getKeysInfo();
        if (!$keys) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Private key is not valid.'));
            return $this->showNotice();
        }
        $apiKey = $keys['privateKey'];
        $this->secure_key = Tools::getValue('secure_key');

        /** Check if cart is valid */
        $cart_id = Tools::getValue('cart_id');
        $cart = new Cart((int)$cart_id);
        if (!Validate::isLoadedObject($cart)) {
            Twispay_Logger::log(sprintf($this->l('[RESPONSE-ERROR]: Cart #%s could not be loaded'), $cart_id));
            return $this->showNotice();
        }

        /** Check if customer is valid */
        $customer = new Customer((int)$cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Twispay_Logger::log(sprintf($this->l('[RESPONSE-ERROR]: Customer #%s could not be loaded.'), $cart->id_customer));
            return $this->showNotice();
        }

        /** Check if the secure key is valid */
        if ($this->secure_key != $customer->secure_key) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Secure key is not valid.'));
            return $this->showNotice();
        }

        $order_id = Order::getOrderByCartId((int)$cart->id);
        if ($order_id && ($this->secure_key == $customer->secure_key)) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Order already validated, order id '). $order_id);
            /**
             * The order has already been placed so we redirect the customer on the confirmation page.
             */
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart_id.'&id_module=' .$module_id.'&id_order='.$order_id.'&key='.$this->secure_key);
        }

        /* Extract the server response and decript it. */
        $decrypted = Twispay_Response::twispay_decrypt_message(/*tw_encryptedResponse*/Tools::getValue('opensslResult') != false ? Tools::getValue('opensslResult') : Tools::getValue('result'), $apiKey);

        /* Check if decryption failed.  */
        if (false === $decrypted) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Decryption failed.'));
            return $this->showNotice();
        } else {
            Twispay_Logger::log($this->l('[RESPONSE]: Decrypted string: ').Tools::jsonEncode($decrypted));
        }

        /** Check if transaction already exist */
        if (Twispay_Transactions::checkTransaction($decrypted['transactionId'])) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Order already validated, transaction id '). $decrypted['transactionId']);
            Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart_id.'&id_module='.$module_id.'&id_order='.$order_id.'&key='.$this->secure_key);
        }

        /* Validate the decripted response. */
        $orderValidation = Twispay_Response::twispay_checkValidation($decrypted, $this->module);

        /* Check if server response validation failed.  */
        if (true !== $orderValidation) {
            Twispay_Logger::log($this->l('[RESPONSE-ERROR]: Validation failed.'));
            return $this->showNotice();
        }

        /** Update the transaction status. */
        Twispay_Status_Updater::updateStatus_backUrl($decrypted, $this);
    }

    public function showNotice()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.7', '>')) {
            return $this->setTemplate('module:twispay/views/templates/front/error_ps17.tpl');
        } else {
            return $this->context->controller->setTemplate('error.tpl');
        }
    }

    public function l($message)
    {
        return $this->module->l($message);
    }
}
