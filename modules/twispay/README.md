# Twispay Credit Card Payments  

Tags: _payment_, _gateway_, _module_  
Requires at least: Prestashop v *1.6.1.0*  
Tested up to: Prestashop v *1.7.6.4*  

Twispay enables new and existing store owners to quickly and effortlessly accept online credit card payments over their Prestashop shop


Description
===========
***Note** :  In case you encounter any difficulties with integration, please contact us at support@twispay.com and we'll assist you through the process.*

Credit Card Payments by Twispay is the official payment module for PrestaShop which allows for a quick and easy integration to Twispay’s Payment Gateway for accepting online credit card payments through a secure environment and a fully customisable checkout process. Give your customers the shopping experience they expect, and boost your online sales with our simple and elegant payment plugin.

[Twispay](https://www.twispay.com) is a European certified acquiring bank with a sleek payment gateway optimized for online shops. We process payments from worldwide customers using Mastercard or Visa debit and credit cards. Increase your purchases by using our conversion rate optimized checkout flow and manage your transactions with our dashboard created specifically for online merchants like you.

Twispay provides merchants with a lean way of accessing a complete portfolio of online payment services at the most competitive rates. For more details concerning our pricing in your area, please check out our [pricing page](https://twispay.com/en/pricing). To use our payment module and start processing you will need a [Twispay merchant account](https://merchant-stage.twispay.com/auth/signup). For any assistance during the on-boarding process, our [sales and compliance](https://www.twispay.com/contact) team are happy to assist you with any enquiries you may have.

We take pride in offering world class, free customer support to all our merchants during the integration phase, and at any time thereafter. Our [support team](https://www.twispay.com/contact) is available non-stop during regular business hours EET.

Our prestashop payment extension allows for fast and easy integration with the Twispay Payment Gateway. Quickly start accepting online credit card payments through a secure environment and a fully customizable checkout process. Give your customers the shopping experience they expect, and boost your online sales with our simple and elegant payment plugin.

At the time of purchase, after checkout confirmation, the customer will be redirected to the secure Twispay Payment Gateway.

All payments will be processed in a secure PCI DSS compliant environment so you don't have to think about any such compliance requirements in your web shop.

Install
=======

### Automatic
1. Connect to the BackOffice of your PrestaShop shop.
2. Go to the Modules tab.
3. Click on the Add a new module link.
4. Download the archive of the registered module on your computer.
5. In the line of the new module, click on Install.
6. Click on Configure.
7. Select **YES** under **Live Mode**. _(Unless you are testing)_
8. Enter your **Live Site ID**. _(Twispay Live Site ID)_
9. Enter your **Live Private key**. _(Twispay Live Private key)_
10. Save your changes.

### Manually
1. Unzip (decompress) the module archive file.
2. Using your FTP software.
3. Place the folder in your PrestaShop /modules folder.
4. Connect to the BackOffice of your shop.
5. Go to Back Office > Modules.
6. Locate the new module in the list, scrolling down if necessary.
5. In the line of the new module, click on Install.
7. Click on Configure.
8. Select **YES** under **Live Mode**. _(Unless you are testing)_
9. Enter your **Live Site ID**. _(Twispay Live Site ID)_
10. Enter your **Live Private key**. _(Twispay Live Private key)_
11. Save your changes.

Changelog
=========
= 1.3.1 =
* Fix an issue with prices over 1000

= 1.3.0 = 
* 1.6.x compatibility 

= 1.1.0 =
* Fix - IPN die() error messages
* Bug fix - if timestamp is array type, use it as an array

= 1.0.1 =
* Updated the way requests are sent to the Twispay server.
* Updated the server response handling to process all the possible server response statuses.
* Added support for refunds.

= 1.0.0 =
* Initial Plugin version
* Merchant config interface
* Integration with Twispay's Secure Hosted Payment Page
* Listening URL which accepts the server’s Instant Payment Notifications
* Replaced FORM used for server notification with JSON
