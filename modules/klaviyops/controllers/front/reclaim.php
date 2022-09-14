<?php
/**
 * Klaviyo
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact extensions@klaviyo.com
 *
 * @author    Klaviyo
 * @copyright Klaviyo
 * @license   commercial
 */

/**
 * Class KlaviyoPsReclaimModuleFrontController
 *
 * Available at /klaviyo/reclaim/cart. Fetch a cart from the db and reload it for the customer.
 */
class KlaviyoPsReclaimModuleFrontController extends ModuleFrontController
{
    /**
     * KlaviyoPsReclaimModuleFrontController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Initialize the controller. Get cart ID from query string and update context. Redirect
     * to cart page while persisting any other query string parameters in request.
     */
    public function init()
    {
        $query_params = Tools::getAllValues();
        $cart_id = $query_params['id_cart'];
        if ($cart_id) {
            // This is intentionally not checked against the user's session to
            // allow for cross-device compatibility when recovering a cart.
            $reclaimed_cart = new Cart($cart_id);
            $this->context->cookie->id_cart = $reclaimed_cart->id;
        }

        // Query string keys that we don't want to persist in the redirect.
        $exclude_keys = ['fc', 'module', 'controller', 'id_cart'];
        $persist_keys = ['action' => 'show'];

        foreach ($query_params as $key => $value) {
            if (!in_array($key, $exclude_keys) && Validate::isUrl($key) && Validate::isUrl($value)) {
                $persist_keys[Tools::safeOutput($key)] = Tools::safeOutput($value);
            }
        }

        Tools::redirect('cart?' . http_build_query($persist_keys));
    }
}
