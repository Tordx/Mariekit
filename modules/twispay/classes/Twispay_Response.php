<?php
/**
 * Twispay Helpers
 *
 * Decodes and validates notifications sent by the Twispay server.
 *
 * @author   Twispay
 * @version  1.0.1
 */

/* Security class check */
if (! class_exists('Twispay_Response')) :
    /**
     * Class that implements methods to decrypt
     * Twispay server responses.
     */
    class Twispay_Response
    {
        /**
         * Decrypt the response from Twispay server.
         *
         * @param string $tw_encryptedMessage - The encripted server message.
         * @param string $tw_secretKey        - The secret key (from Twispay).
         *
         * @return Array([key => value,]) - If everything is ok array containing the decrypted data.
         *         bool(FALSE)            - If decription fails.
         */
        public static function Twispay_decrypt_message($tw_encryptedMessage, $tw_secretKey)
        {
            $encrypted = ( string )$tw_encryptedMessage;

            if (!strlen($encrypted) || (false == strpos($encrypted, ','))) {
                return false;
            }

            /* Get the IV and the encrypted data */
            $encryptedParts = explode(/*delimiter*/',', $encrypted, /*limit*/2);
            $iv = base64_decode($encryptedParts[0]);
            if (false === $iv) {
                return false;
            }

            $encryptedData = base64_decode($encryptedParts[1]);
            if (false === $encryptedData) {
                return false;
            }

            /* Decrypt the encrypted data */
            $decryptedResponse = openssl_decrypt($encryptedData, /*method*/'aes-256-cbc', $tw_secretKey, /*options*/OPENSSL_RAW_DATA, $iv);

            if (false === $decryptedResponse) {
                return false;
            }

            /* JSON decode the decrypted data. */
            $decryptedResponse = json_decode($decryptedResponse, /*assoc*/true, /*depth*/4);

            /* Normalize values */
            $decryptedResponse['status'] = (empty($decryptedResponse['status'])) ? ($decryptedResponse['transactionStatus']) : ($decryptedResponse['status']);

            /** Check if externalOrderId uses '_' separator */
            if (strpos($decryptedResponse['externalOrderId'], '_') !== false) {
              $explodedVal = explode('_', $decryptedResponse['externalOrderId'])[0];
              /** Check if externalOrderId contains only digits and is not empty */
              if(!empty($explodedVal) && ctype_digit($explodedVal)){
                 $decryptedResponse['externalOrderId'] = pSQL($explodedVal);
              }
            }

            $decryptedResponse['cardId'] = (!empty($decryptedResponse['cardId'])) ? ($decryptedResponse['cardId']) : (0);

            return $decryptedResponse;
        }

        /**
         * Function that validates a decripted response.
         *
         * @param tw_response The server decripted and JSON decoded response
         * @param module Module instance
         *
         * @return bool(FALSE)     - If any error occurs
         *         bool(TRUE)      - If the validation is successful
         */

        public static function twispay_checkValidation($tw_response, $module)
        {
            $tw_errors = array();

            if (!$tw_response) {
                return false;
            }
            /** Check if transaction status exists */
            if (empty($tw_response['status']) && empty($tw_response['transactionStatus'])) {
                $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty status.');
            }
            /** Check if identifier exists */
            if (empty($tw_response['identifier'])) {
                $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty identifier.');
            }
            /** Check if external order id exists */
            if (empty($tw_response['externalOrderId'])) {
                $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty externalOrderId.');
            }
            /** Check if transaction id exists */
            if (empty($tw_response['transactionId'])) {
                $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty transactionId.');
            }
            /** Check if external order id is a definex prestashop cart id */
            $id_cart = (!empty($tw_response['externalOrderId'])) ? $tw_response['externalOrderId'] : 0;
            $cart = new Cart($id_cart);
            $cartFound = false;
            if (Validate::isLoadedObject($cart)) {
                $cartFound = true;
            }
            /** Check if amout exists and format and cast it in a proper format if yes */
            if (empty($tw_response['amount'])) {
                if ($cartFound) {
                    $tw_response['amount'] = (float)number_format((float)$cart->getOrderTotal(true, Cart::BOTH), 2, '.', '');
                } else {
                    $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty amount');
                }
            }
            /** Check if currency exists and format and assign its ISO code if yes */
            if (empty($tw_response['currency'])) {
                if ($cartFound) {
                    $currency = new Currency($cart->id_currency);
                    if (Validate::isLoadedObject($currency)) {
                        $tw_response['currency'] = $currency->iso_code;
                    } else {
                        $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty currency');
                    }
                } else {
                    $tw_errors[] = $module->l('[RESPONSE-ERROR]: Empty currency');
                }
            }

            /** Check if status is valid */
            if (!in_array($tw_response['status'], Twispay_Status_Updater::$RESULT_STATUSES)) {
                $tw_errors[] = $module->l('[RESPONSE-ERROR]: Wrong status: ').$tw_response['status'];
            }

            /** Check for error and log them all */
            if (sizeof($tw_errors)) {
                foreach ($tw_errors as $err) {
                    Twispay_Logger::log($err);
                }
                return false;
            /** If the response is valid */
            } else {
                /** Prepare the data object related to transaction table format */
                $data = [ 'status'          => $tw_response['status']
                        , 'id_cart'         => (int)$tw_response['externalOrderId']
                        , 'identifier'      => $tw_response['identifier']
                        , 'customerId'      => (int)$tw_response['customerId']
                        , 'orderId'         => (int)$tw_response['orderId']
                        , 'cardId'          => (int)$tw_response['cardId']
                        , 'transactionId'   => (int)$tw_response['transactionId']
                        , 'transactionKind' => $tw_response['transactionKind']
                        , 'amount'          => (float)$tw_response['amount']
                        , 'currency'        => $tw_response['currency']
                        , 'timestamp'       => $tw_response['timestamp']];

                /** Insert the new transaction */
                Twispay_Transactions::insertTransaction($data);
                Twispay_Logger::log($module->l('[RESPONSE]: Data: ').Tools::jsonEncode($data));
                Twispay_Logger::log($module->l('[RESPONSE]: Validating completed for cart ID: ').$data['id_cart']);
                return true;
            }
        }
    }
endif; /* End if class_exists. */
