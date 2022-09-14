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

namespace KlaviyoPs\Classes;

use Cart;
use Db;
use Image;
use Product;

use KlaviyoPs\Classes\BusinessLogicServices\ProductPayloadService;

/**
 * Class KlaviyoUtils is a collection of utility methods reused across multiple KlaviyoPs module classes.
 * @package KlaviyoPs\Classes
 */
class KlaviyoUtils
{
    /**
     * Get full url for product image based on attribute ID, otherwise get cover image.
     *
     * @param $productId
     * @return string
     */
    public static function getProductImageLink($product_id, $product_attribute_id, $shop_id, $lang_id)
    {
        if ($product_attribute_id) {
            $image = Image::getBestImageAttribute(
                $shop_id, $lang_id, $product_id, $product_attribute_id
            );
        }
        // Some product attributes don't correspond to a different image e.g. size. Handle that here as well.
        if (!isset($image) || !$image) {
            $image = Image::getCover($product_id);
        }

        if (is_array($image) && isset($image['id_image'])) {
            $image = new Image($image['id_image']);
            return _PS_BASE_URL_ . _THEME_PROD_DIR_ . $image->getExistingImgPath() . ".jpg";
        }
    }

    /**
     * Build object containing values compiled from and including cart line items e.g. unique categories.
     *
     * @param Cart $cart
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public static function buildCartLineItemsArray($cart)
    {
        $langId = $cart->id_lang;
        $shopId = $cart->id_shop;

        // Define total cart variables.
        $itemCount = 0;
        $itemNames = array();
        $lineItems = array();
        $productCategories = array();
        $productTags = array();

        $products = $cart->getProducts();
        foreach ($products as $product) {
            $productId = $product['id_product'];
            $productObj = new Product($productId, false, $id_lang = $langId, $id_shop = $shopId);

            foreach (Product::getProductCategoriesFull($id_product = $productId, $id_lang = $langId) as $category) {
                $category_name = $category['name'];
                if (!in_array($category_name, $productCategories)) {
                    $productCategories[] = $category_name;
                }
            };

            $tags = ProductPayloadService::getProductTagsArray($productId, $langId);
            foreach ($tags as $tag) {
                if (!in_array($tag, $productTags)) {
                    $productTags[] = $tag;
                }
            }

            $itemNames[] = $product['name'];
            $lineItems[] = array(
                'Image' => KlaviyoUtils::getProductImageLink($productId, $product['id_product_attribute'], $shopId, $langId),
                'ProductURL' => ProductPayloadService::getProductUrl($productId, $langId, $shopId),
                'ProductID' => $productId,
                'Price' => number_format($product['price'], 2),
                'Quantity' => $product['quantity'],
                'ProductUniqueID' => $product['unique_id'],
                'ProductInfo' => $product,
                'Tags' => $tags,
            );

            $itemCount += (int) $product['quantity'];
        };

        return array(
            'lineItems' => $lineItems,
            'itemNames' => $itemNames,
            'itemCount' => $itemCount,
            'uniqueCategories' => $productCategories,
            'uniqueTags' => $productTags
        );
    }

    /**
     * Build url to reclaim cart.
     *
     * @param $shop_url
     * @param $id_cart
     * @param $id_shop
     * @return string
     */
    public static function buildReclaimCartUrl($shop_url, $id_cart, $id_shop)
    {
        return sprintf(
            '%s/klaviyo/reclaim/cart?id_cart=%s&id_shop=%s', trim($shop_url, '/'), $id_cart, $id_shop
        );
    }

    /**
     * Get order status mappings for all stores where configured.
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public static function getAllOrderStatusMaps()
    {
        $sql = 'SELECT `id_shop`, `value`, `date_add`, `date_upd` FROM ' . _DB_PREFIX_ . 'configuration WHERE `name` = "KLAVIYO_ORDER_STATUS_MAP"';
        $result = Db::getInstance()->ExecuteS($sql);

        $statusMaps = array();
        foreach ($result as $statusMap) {
            $statusMaps[$statusMap['id_shop']] = array(
                'map' => json_decode($statusMap['value'], true),
                'date_add' => $statusMap['date_add'],
                'date_upd' => $statusMap['date_upd'],
            );
        }
        return $statusMaps;
    }
}
