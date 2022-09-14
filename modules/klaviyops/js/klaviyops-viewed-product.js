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

window.addEventListener('load', function () {
    var _learnq = window._learnq || [];

    var klItem = {
        $value: klProduct.Price,
        ProductName: klProduct.ProductName,
        ProductID: klProduct.ProductID,
        Price: klProduct.Price,
        SpecialPrice: klProduct.SpecialPrice,
        Categories: klProduct.Categories,  // The list of categories is an array of strings.
        Tags: klProduct.Tags,
        ImageURL: klProduct.Image,
        URL: klProduct.Link,
        ShopID: klProduct.ShopID,
        LangID: klProduct.LangID
    }

    _learnq.push(['track', 'Viewed Product', klItem]);
    _learnq.push(['trackViewedItem', {
        Title: klItem.ProductName,
        ItemId: klItem.ProductID,
        Categories: klItem.Categories,
        ImageUrl: klItem.ImageURL,
        Url: klItem.URL,
        Metadata: {
            Price: klItem.Price,
        }
    }]);
});
