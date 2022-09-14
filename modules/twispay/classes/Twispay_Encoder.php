<?php
/**
 * Twispay Helpers
 *
 * Encodes notifications sent to the twispay platform.
 *
 * @author   Twispay
 * @version  1.0.1
 */

/* Security class check */
if (! class_exists('Twispay_Encoder')) :
    /**
     * Class that implements methods to get the value
     * of `jsonRequest` and `checksum` that need to be
     * sent by POST when making a Twispay order.
     */
    class Twispay_Encoder
    {
        /**
         * Get the `jsonRequest` parameter (order parameters as JSON and base64 encoded).
         *
         * @param array $orderData The order parameters.
         *
         * @return string
         */
        public static function getBase64JsonRequest(array $orderData)
        {
            return base64_encode(Tools::jsonEncode($orderData));
        }

        /**
         * Get the `checksum` parameter (the checksum computed over the `jsonRequest` and base64 encoded).
         *
         * @param array $orderData The order parameters.
         * @param string $secretKey The secret key (from Twispay).
         *
         * @return string
         */
        public static function getBase64Checksum(array $orderData, $secretKey)
        {
            $hmacSha512 = hash_hmac(/*algo*/'sha512', Tools::jsonEncode($orderData), $secretKey, /*raw_output*/true);
            return base64_encode($hmacSha512);
        }
    }
endif; /* End if class_exists. */
