/**
 * @author   Twispay
 * @version  1.0.1
 */

twispay_payment_interval = setInterval(function() {
    if (!document.getElementById('twispay_payment_form')) {
        return;
    }
    clearInterval(twispay_payment_interval);
    document.getElementById('twispay_payment_form').submit();
}, 100);
