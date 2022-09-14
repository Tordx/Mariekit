<?php
/**
 * Twispay Helpers
 *
 * Logs messages and transactions.
 *
 * @author   Twispay
 * @version  1.0.1
 */

/* Security class check */
if (! class_exists('Twispay_Transactions')) :
    /**
     * Class that implements custom transaction table and the assigned operations
     */
    class Twispay_Transactions
    {
        /**
         * Function that initialize the database table twispay_transactions.
         */
        public static function createTransactionsTable()
        {
            $sql = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."twispay_transactions` (
                     `id_transaction` int(10) NOT NULL AUTO_INCREMENT,
                     `status` varchar(50) NOT NULL,
                     `id_cart` int(10) NOT NULL,
                     `identifier` varchar(50) NOT NULL,
                     `customerId` int(10) NOT NULL,
                     `orderId` int(10) NOT NULL,
                     `cardId` int(10) NOT NULL,
                     `transactionId` int(10) NOT NULL,
                     `transactionKind` varchar(50) NOT NULL,
                     `amount` float NOT NULL,
                     `currency` varchar(8) NOT NULL,
                     `date` DATETIME NOT NULL,
                     PRIMARY KEY (`id_transaction`)
                   ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;";
            return Db::getInstance()->execute($sql);
        }

        /**
         * Function that insert a recording into twispay_transactions table.
         *
         * @param array([key => value]) data - Array of data to be populated
         *
         * @return array([key => value]) - The data that was added
         *
         */
        public static function insertTransaction($data)
        {
            /** Define filtred keys */
            $columns = array(
              'status',
              'id_cart',
              'identifier',
              'customerId',
              'orderId',
              'cardId',
              'transactionId',
              'transactionKind',
              'amount',
              'currency',
              'date',
            );
            /** Convert data value to mysql format */
            if (!empty($data['timestamp'])) {
                if (is_array($data['timestamp'])) {
                    $data['date'] = date('Y-m-d H:i:s', strtotime($data['timestamp']['date']));
                } else {
                    $data['date'] = date('Y-m-d H:i:s', $data['timestamp']);
                }
                unset($data['timestamp']);
            }
            /** Filter data values and construct the insert query */
            foreach (array_keys($data) as $key) {
                if (!in_array($key, $columns)) {
                    unset($data[$key]);
                } else {
                    $data[$key] = pSQL($data[$key]);
                }
            }
            /** Keep just the customer id from identifier */
            if (!empty($data['identifier']) && strpos($data['identifier'], '_') !== false) {
               $explodedVal = explode("_", $data['identifier'])[2];
               /** Check if customer id contains only digits and is not empty */
               if(!empty($explodedVal) && ctype_digit($explodedVal)){
            	   $data['identifier'] = pSQL($explodedVal);
               }
            }

            Db::getInstance()->insert('twispay_transactions', $data);
            return $data;
        }

        /**
         * Function that returns the paged list of transactions
         *
         * @param int page - The selected page
         * @param int selected_pagination - The number of results per page
         *
         * @return array[array([key=>value])] - List of transactions
         *
         */
        public static function getTransactions($page, $selected_pagination)
        {
            if ((int)$page <= 0) {
                $page = 1;
            }
            $limit = ((int)$page-1)*$selected_pagination;
            return Db::getInstance()->executeS(
                'SELECT tt.*, o.`reference`
                                                AS `order_reference`, CONCAT(tt.`amount`, " ", tt.`currency`)
                                                AS `amount_formatted`, CONCAT(c.`firstname`," ", c.`lastname`)
                                                AS `customer_name`  FROM `'._DB_PREFIX_.'twispay_transactions` tt
                                                LEFT JOIN `'._DB_PREFIX_.'orders` o LEFT JOIN `'._DB_PREFIX_.'customer` c
                                                ON (c.`id_customer` = o.`id_customer`) ON (o.`id_cart` = tt.`id_cart`)
                                                ORDER BY `id_transaction` DESC LIMIT '. (int)$limit .', '.(int)$selected_pagination
                                              );
        }

        /**
         * Function that returns the transaction based on the cart id field
         *
         * @param int id_cart - The cart id for which the transaction is searched
         *
         ** @return array([key=>value]) - The transaction array
         *
        **/
        public static function getTransactionByCartId($id_cart)
        {
            $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'twispay_transactions` WHERE `id_cart`='.(int)$id_cart);
            return $result?$result[0]:false;
        }

        /**
         * Function that check if a tansaction exists
         *
         * @param int id - The id of the transaction to be checked
         *
         ** @return bool(TRUE|FALSE) - Transaction existance
         *
        **/
        public static function checkTransaction($id)
        {
            $result = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'twispay_transactions` WHERE `transactionId`='.(int)$id);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Function that returns the number of twispay_transactions
         *
         ** @return int - total number of transactions
         *
        **/
        public static function getTransactionsNumber()
        {
            return (int)Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'twispay_transactions`');
        }

        /**
         * Function that call the refund operation via Twispay API and update the local order based on the response.
         *
         * @param array transaction - Twispay transaction info
         * @param array keys - Api keys
         * @param string module: Module instance use for accessing runtime values like configuration, active language, etc.
         *
         * @return array([key => value,]) - string 'status'         - API Message
         *                                  string 'rawdata'        - Unprocessed response
         *                                  string 'id_transaction' - The twispay id of the refunded transaction
         *                                  string 'id_cart'        - The opencart id of the canceled order
         *                                  boolean 'refunded'      - Operation success indicator
         *
         */
        public static function refundTransaction($transaction, $keys, $module)
        {
            /** Create the post message */
            $postData = 'amount=' . $transaction['amount'] . '&' . 'message=' . 'Refund for order ' . $transaction['orderId'];
            /** Define the URL for cURL operation */
            if ($keys['liveMode']) {
                $url = 'https://api.twispay.com/transaction/' . $transaction['transactionId'];
            } else {
                $url = 'https://api-stage.twispay.com/transaction/' . $transaction['transactionId'];
            }

            /** Create a new cURL session. */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Authorization: ' . $keys['privateKey']]);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            $response = curl_exec($ch);
            curl_close($ch);
            $json = json_decode($response);

            /** Check if curl decode fails */
            if (!isset($json)) {
                $json = new stdClass();
                $json->message = $module->l('json_decode_error');
                Twispay_Logger::api_log($module->l('json_decode_error'));
            }

            if ($json->code == 200 && $json->message == 'Success') {
                $data = array(
                 'status'          => Twispay_Status_Updater::$RESULT_STATUSES['REFUND_OK'],
                 'rawdata'         => $json,
                 'id_transaction'  => $transaction['transactionId'],
                 'id_cart'         => $transaction['id_cart'],
                 'refunded'        => 1,
                );
            } else {
                $data = array(
                 'status'          => isset($json->error)?$json->error[0]->message:$json->message,
                 'rawdata'         => $json,
                 'id_transaction'  => $transaction['transactionId'],
                 'id_cart'         => $transaction['id_cart'],
                 'refunded'        => 0,
                );
            }
            return $data;
        }
    }
endif; /* End if class_exists. */
