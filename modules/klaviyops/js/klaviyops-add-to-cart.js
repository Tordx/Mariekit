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

var _learnq = _learnq || [];

function klAddToCart() {
    $.ajax({
        url: '/klaviyo/events/add-to-cart',
        type: 'POST',
        dataType: 'json',
        success: function (r) {
            if (typeof r.errors != 'undefined' && !r.errors) {
                _learnq.push(['track', 'Added to Cart', r.data]);
            }
            if (!!localStorage.getItem('klaviyops_debug')) {
                console.log(r);
            }
        }
    });
}

if (typeof (prestashop) != 'undefined') {
    $(document).ready(function() {
        prestashop.on('updateCart', klAddToCart);
    });
}
