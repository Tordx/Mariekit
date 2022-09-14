/**
 * @author   Twispay
 * @version  1.0.1
 */

$(function() {
    /** Triggers for hiding and showing LIVE/STAGING INPUTS */
    $(document).ready(TwispayCheckLiveOrStaging);
    $(document).on('change', 'input[name="TWISPAY_LIVE_MODE"]', TwispayCheckLiveOrStaging);
});

/** Function to hide or show LIVE/STAGING inputs on module configuration page */
function TwispayCheckLiveOrStaging() {
    if (!$(document).find('input[name="TWISPAY_LIVE_MODE"]:checked').length) {
        return;
    }
    var isLive = parseInt($(document).find('input[name="TWISPAY_LIVE_MODE"]:checked').val());
    /** If the live mode is chacked */
    if (isLive) {
        /** Hide - Staging - Site ID / Private Key */
        $('#TWISPAY_SITEID_STAGING, #TWISPAY_PRIVATEKEY_STAGING').closest('.form-group').slideUp();
        /** Show - Live - Site ID / Private Key */
        $('#TWISPAY_SITEID_LIVE, #TWISPAY_PRIVATEKEY_LIVE').closest('.form-group').slideDown();
    }
    else {
        /** Show - Staging - Site ID / Private Key */
        $('#TWISPAY_SITEID_STAGING, #TWISPAY_PRIVATEKEY_STAGING').closest('.form-group').slideDown();
        /** Hide - Live - Site ID / Private Key */
        $('#TWISPAY_SITEID_LIVE, #TWISPAY_PRIVATEKEY_LIVE').closest('.form-group').slideUp();
    }
}
