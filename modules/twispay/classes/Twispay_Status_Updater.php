<?php
/**
 * Twispay Helpers
 *
 * Updates the statused of orders and subscriptions based
 *  on the status read from the server response.
 *
 * @author   Twispay
 * @version  1.0.1
 */

/* Security class check */
if (! class_exists('Twispay_Status_Updater')) :
    /**
     * Class that implements methods to update the statuses
     * of orders and subscriptions based on the status received
     * from the server.
     */
    class Twispay_Status_Updater
    {
        /* Array containing the possible result statuses. */
        public static $RESULT_STATUSES = [ 'UNCERTAIN' => 'uncertain' /* No response from provider */
                                         , 'IN_PROGRESS' => 'in-progress' /* Authorized */
                                         , 'COMPLETE_OK' => 'complete-ok' /* Captured */
                                         , 'COMPLETE_FAIL' => 'complete-failed' /* Not authorized */
                                         , 'CANCEL_OK' => 'cancel-ok' /* Capture reversal */
                                         , 'REFUND_OK' => 'refund-ok' /* Settlement reversal */
                                         , 'VOID_OK' => 'void-ok' /* Authorization reversal */
                                         , 'CHARGE_BACK' => 'charge-back' /* Charge-back received */
                                         , 'THREE_D_PENDING' => '3d-pending' /* Waiting for 3d authentication */
                                         , 'EXPIRING' => 'expiring' /* The recurring order has expired */
                                         , 'REFUND_REQUESTED' => 'refund-requested' /* The recurring order has expired */
                                         ];

        /**
         * Update the status of an order according to the received server status.
         *
         * @param object decrypted: Decrypted order message.
         * @param string controller: Controller instance use for accessing runtime values like configuration, active language, etc.
         *
         * @return void
         */
        public static function updateStatus_backUrl($decrypted, $controller)
        {
            /** Get the cart id */
            $cart_id = (int)$decrypted['externalOrderId'];
            $module = $controller->module;

            $completed_ok = false;
            $amount = 0;
            switch ($decrypted['status']) {
                case Twispay_Status_Updater::$RESULT_STATUSES['COMPLETE_FAIL']:
                    /** Mark order as Failed. */
                    $status_id = Configuration::get('PS_OS_ERROR');
                    $order_message = $module->l('Twispay payment failed');
                    Twispay_Logger::log($module->l('[RESPONSE]: Status failed for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['THREE_D_PENDING']:
                    /** Mark order as Pending. */
                    $status_id = Configuration::get('PS_OS_PREPARATION');
                    $order_message = $module->l('Twispay payment is pending');
                    Twispay_Logger::log($module->l('[RESPONSE]: Status pending for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['IN_PROGRESS']:
                case Twispay_Status_Updater::$RESULT_STATUSES['COMPLETE_OK']:
                    /** Mark order as Processing. */
                    $status_id = Configuration::get('PS_OS_PAYMENT');
                    $order_message =  $module->l('Paid Twispay');
                    $completed_ok = true;
                    $amount = $decrypted['amount'];
                    Twispay_Logger::log($module->l('[RESPONSE]: Status complete-ok for cart ID: ').$cart_id);
                break;

                default:
                    Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Wrong status: ').$decrypted['status']);
                    return $controller->showNotice();
                break;
            }

            /** Check if cart is valid */
            $cart = new Cart($cart_id);
            if (!Validate::isLoadedObject($cart)) {
                Twispay_Logger::log(sprintf($module->l('[RESPONSE-ERROR]: Cart #%s could not be loaded.'), $cart_id));
                return $controller->showNotice();
            }

            /** Check if customer is valid */
            $id_customer = $cart->id_customer;
            $decrypted['customerId'] = $id_customer;
            $customer = new Customer($id_customer);
            if (!Validate::isLoadedObject($customer)) {
                Twispay_Logger::log(sprintf($module->l('[RESPONSE-ERROR]: Customer #%s could not be loaded.'), $id_customer));
                return $controller->showNotice();
            }

            /** Check if currency is valid */
            $id_currency = (int)Currency::getIdByIsoCode($decrypted['currency']);
            if (!$id_currency) {
                Twispay_Logger::log($module->l($module->l('[RESPONSE-ERROR]: Wrong Currency: '). $decrypted['currency']));
                return $controller->showNotice();
            }

            /** Check if status is valid */
            if ($status_id) {
                $order_id = Order::getOrderByCartId($cart->id);
                /** Check if order exists */
                if ($order_id) {
                    $order = new Order($order_id);
                    Twispay_Logger::log($module->l('[RESPONSE]: Order updated.'));
                    if ($amount!=0 && !$order->addOrderPayment($amount, null, null)) {
                        Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Order payment registration failed'));
                        return $controller->showNotice();
                    }
                    $order->setCurrentState($status_id);
                /** If order didn't exist create a new one */
                } else {
                    if ($controller->module->validateOrder(
                        $cart_id,
                        $status_id,
                        $amount,
                        $controller->module->displayName,
                        $order_message,
                        null,
                        $id_currency,
                        false,
                        $customer->secure_key
                    )) {
                        $order_id = Order::getOrderByCartId($cart_id);
                        Twispay_Logger::log($module->l('[RESPONSE]: Order created.'));
                    } else {
                        Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Could not validate order'));
                        return $controller->showNotice();
                    }
                }
            }

            if ($completed_ok) {
                Tools::redirect('index.php?controller=order-confirmation&id_cart='.$cart_id.'&id_module=' .$controller->module->id.'&id_order='.$order_id.'&key='.$controller->secure_key);
            } else {
                return $controller->showNotice();
            }
        }

        /**
         * Update the status of an order according to the received server status.
         *
         * @param object decrypted: Decrypted order message.
         * @param object module: The module instance
         * @param string controller: Controller instance use for accessing runtime values like configuration, active language, etc.
         *
         * @return void
         */
        public static function updateStatus_ipn($decrypted, $controller)
        {
            /** Get the cart id */
            $cart_id = $decrypted['externalOrderId'];
            $module = $controller->module;

            $completed_ok = false;
            $amount = 0;
            switch ($decrypted['status']) {
                case Twispay_Status_Updater::$RESULT_STATUSES['EXPIRING']:
                case Twispay_Status_Updater::$RESULT_STATUSES['CANCEL_OK']:
                case Twispay_Status_Updater::$RESULT_STATUSES['VOID_OK']:
                case Twispay_Status_Updater::$RESULT_STATUSES['CHARGE_BACK']:
                    /** Mark order as Canceled. */
                    $status_id = Configuration::get('PS_OS_CANCELED');
                    $order_message = $module->l('Twispay payment was canceled');
                    Twispay_Logger::log($module->l('[RESPONSE]: Status canceled for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['REFUND_OK']:
                    /** Mark order as Refunded. */
                    $status_id = Configuration::get('PS_OS_REFUND');
                    $order_message = $module->l('Twispay payment was refunded');
                    $amount = $decrypted['amount']*-1;
                    Twispay_Logger::log($module->l('[RESPONSE]: Status refunded for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['COMPLETE_FAIL']:
                    /** Mark order as Failed. */
                    $status_id = Configuration::get('PS_OS_ERROR');
                    $order_message = $module->l('Twispay payment failed');
                    Twispay_Logger::log($module->l('[RESPONSE]: Status failed for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['THREE_D_PENDING']:
                    /** Mark order as Pending. */
                    $status_id = Configuration::get('PS_OS_PREPARATION');
                    $order_message = $module->l('Twispay payment is pending');
                    Twispay_Logger::log($module->l('[RESPONSE]: Status pending for cart ID: ').$cart_id);
                break;

                case Twispay_Status_Updater::$RESULT_STATUSES['IN_PROGRESS']:
                case Twispay_Status_Updater::$RESULT_STATUSES['COMPLETE_OK']:
                    /** Mark order as Processing. */
                    $status_id = Configuration::get('PS_OS_PAYMENT');
                    $order_message =  $module->l('Paid Twispay');
                    $completed_ok = true;
                    $amount = $decrypted['amount'];
                    Twispay_Logger::log($module->l('[RESPONSE]: Status complete-ok for cart ID: ').$cart_id);
                break;

                default:
                    Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Wrong status: ').$decrypted['status']);
                    return false;
                break;
            }

            /** Check if cart is valid */
            $cart = new Cart($cart_id);
            if (!Validate::isLoadedObject($cart)) {
                Twispay_Logger::log(sprintf($module->l('[RESPONSE-ERROR]: Cart #%s could not be loaded'), $cart_id));
                return false;
            }

            /** Check if customer is valid */
            $id_customer = $cart->id_customer;
            $decrypted['customerId'] = $id_customer;
            $customer = new Customer($id_customer);
            if (!Validate::isLoadedObject($customer)) {
                Twispay_Logger::log(sprintf($module->l('[RESPONSE-ERROR]: Customer #%s could not be loaded.'), $id_customer));
                return false;
            }

            /** Check if currency is valid */
            $id_currency = (int)Currency::getIdByIsoCode($decrypted['currency']);
            if (!$id_currency) {
                Twispay_Logger::log($module->l($module->l('[RESPONSE-ERROR]: Wrong Currency: '). $decrypted['currency']));
                return false;
            }

            /** Check if status is valid */
            if ($status_id) {
                $order_id = Order::getOrderByCartId($cart->id);
                /** Check if order exist */
                if ($order_id) {
                    $order = new Order($order_id);
                    Twispay_Logger::log($module->l('[RESPONSE]: Order updated.'));
                    if ($amount!=0 && !$order->addOrderPayment($amount, null, null)) {
                        Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Order payment registration failed'));
                        return false;
                    }
                    $order->setCurrentState($status_id);
                /** If order did not exist create a new one */
                } else {
                    if ($controller->module->validateOrder(
                        $cart_id,
                        $status_id,
                        $amount,
                        $controller->module->displayName,
                        $order_message,
                        null,
                        $id_currency,
                        false,
                        $customer->secure_key
                    )) {
                        Twispay_Logger::log($module->l('[RESPONSE]: Order created.'));
                    } else {
                        Twispay_Logger::log($module->l('[RESPONSE-ERROR]: Could not validate order'));
                        return false;
                    }
                }
            }
            if ($completed_ok) {
                return true;
            } else {
                return false;
            }
        }
    }
endif; /* End if class_exists. */
