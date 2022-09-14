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

window.addEventListener('load', function() {
    if (klStartedCheckout.email){
        ajaxBuildReclaim(klStartedCheckout.email);
    } else {
        $(klStartedCheckout.emailInputSelector).on('change', function(){
            var klInputEmail = $(this).val();
            setKlaviyoCookie({'$email': klInputEmail});
            ajaxBuildReclaim(klInputEmail);
        })
    }
});

function ajaxBuildReclaim(email) {
    var buildReclaimPayload = {
        ajax: true,
        token: klStartedCheckout.token,
        email: email,
        cartId: klStartedCheckout.cartId,
        module: 'klaviyops',
        fc: 'module',
        controller: 'buildReclaim',
    };
    $.ajax({
        type: 'POST',
        data: buildReclaimPayload,
        url: klStartedCheckout.baseUrl,
        success: function(r) {
            if (!!localStorage.getItem('klaviyops_debug')) {
                console.log(r);
            }
        }
    });
}

function setKlaviyoCookie(cookie_data) {
    cvalue = btoa(JSON.stringify(cookie_data));
    var date = new Date();
    date.setTime(date.getTime() + (63072e6));  // Expiration set for 2 years.
    var expires = "expires=" + date.toUTCString();
    document.cookie = "__kla_id=" + cvalue + ";" + expires + "; path=/";
}
