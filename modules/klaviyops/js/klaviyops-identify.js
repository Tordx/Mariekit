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

if (klCustomer.email){
    _learnq.push(['identify', {$email: klCustomer.email}]);

    if (klCustomer.firstName){
        _learnq.push(['identify', {$first_name: klCustomer.firstName}]);
    }
    if (klCustomer.lastName){
        _learnq.push(['identify', {$last_name: klCustomer.lastName}]);
    }
}
