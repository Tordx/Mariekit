<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(__DIR__);
$baseDir = dirname($vendorDir);

return array(
    'Composer\\InstalledVersions' => $vendorDir . '/composer/InstalledVersions.php',
    'Sendinblue' => $baseDir . '/sendinblue.php',
    'SendinblueCallbackModuleFrontController' => $baseDir . '/controllers/front/callback.php',
    'SendinblueTabController' => $baseDir . '/controllers/admin/SendinblueTabController.php',
    'Sendinblue\\Factories\\EventDataFactory' => $baseDir . '/factories/EventDataFactory.php',
    'Sendinblue\\Factories\\HooksFactory' => $baseDir . '/factories/HooksFactory.php',
    'Sendinblue\\Factories\\ProductFactory' => $baseDir . '/factories/ProductFactory.php',
    'Sendinblue\\Factories\\ProductVariantsFactory' => $baseDir . '/factories/ProductVariantsFactory.php',
    'Sendinblue\\Hooks\\AbstractHook' => $baseDir . '/hooks/AbstractHook.php',
    'Sendinblue\\Hooks\\ActionCartSaveHook' => $baseDir . '/hooks/ActionCartSaveHook.php',
    'Sendinblue\\Hooks\\ActionCustomerAccountAddHook' => $baseDir . '/hooks/ActionCustomerAccountAddHook.php',
    'Sendinblue\\Hooks\\ActionCustomerAccountUpdateHook' => $baseDir . '/hooks/ActionCustomerAccountUpdateHook.php',
    'Sendinblue\\Hooks\\ActionEmailConfigurationSaveHook' => $baseDir . '/hooks/ActionEmailConfigurationSaveHook.php',
    'Sendinblue\\Hooks\\ActionNewsletterRegistrationAfterHook' => $baseDir . '/hooks/ActionNewsletterRegistrationAfterHook.php',
    'Sendinblue\\Hooks\\ActionObjectCustomerAddressUpdateHook' => $baseDir . '/hooks/ActionObjectCustomerAddressUpdateHook.php',
    'Sendinblue\\Hooks\\ActionOrderStatusUpdateHook' => $baseDir . '/hooks/ActionOrderStatusUpdateHook.php',
    'Sendinblue\\Hooks\\OrderConfirmationHook' => $baseDir . '/hooks/OrderConfirmationHook.php',
    'Sendinblue\\Models\\AbstractModel' => $baseDir . '/models/AbstractModel.php',
    'Sendinblue\\Models\\CartItem' => $baseDir . '/models/CartItem.php',
    'Sendinblue\\Models\\CartProperties' => $baseDir . '/models/CartProperties.php',
    'Sendinblue\\Models\\CustomerAddress' => $baseDir . '/models/CustomerAddress.php',
    'Sendinblue\\Models\\EventdataData' => $baseDir . '/models/EventdataData.php',
    'Sendinblue\\Models\\OrderMiscellaneous' => $baseDir . '/models/OrderMiscellaneous.php',
    'Sendinblue\\Models\\OrderPayload' => $baseDir . '/models/OrderPayload.php',
    'Sendinblue\\Models\\Product' => $baseDir . '/models/Product.php',
    'Sendinblue\\Models\\ProductVariant' => $baseDir . '/models/ProductVariant.php',
    'Sendinblue\\Models\\TransactionalOrderPayload' => $baseDir . '/models/TransactionalOrderPayload.php',
    'Sendinblue\\Services\\ApiClientService' => $baseDir . '/services/ApiClientService.php',
    'Sendinblue\\Services\\ConfigService' => $baseDir . '/services/ConfigService.php',
    'Sendinblue\\Services\\CustomerService' => $baseDir . '/services/CustomerService.php',
    'Sendinblue\\Services\\IntegrationClient' => $baseDir . '/services/IntegrationClient.php',
    'Sendinblue\\Services\\NewsletterRecipientService' => $baseDir . '/services/NewsletterRecipientService.php',
    'Sendinblue\\Services\\ProductService' => $baseDir . '/services/ProductService.php',
    'Sendinblue\\Services\\SmsService' => $baseDir . '/services/SmsService.php',
    'Sendinblue\\Services\\SubscriptionService' => $baseDir . '/services/SubscriptionService.php',
    'Sendinblue\\Services\\WebserviceService' => $baseDir . '/services/WebserviceService.php',
    'WebserviceSpecificManagementSendinblueAbstract' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinblueAbstract.php',
    'WebserviceSpecificManagementSendinblueconfig' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinblueconfig.php',
    'WebserviceSpecificManagementSendinbluecustomers' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinbluecustomers.php',
    'WebserviceSpecificManagementSendinbluedisconnect' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinbluedisconnect.php',
    'WebserviceSpecificManagementSendinblueinfo' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinblueinfo.php',
    'WebserviceSpecificManagementSendinbluenewsletterrecipients' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinbluenewsletterrecipients.php',
    'WebserviceSpecificManagementSendinblueproducts' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinblueproducts.php',
    'WebserviceSpecificManagementSendinbluesendtestmail' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinbluesendtestmail.php',
    'WebserviceSpecificManagementSendinbluetest' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinbluetest.php',
    'WebserviceSpecificManagementSendinblueunsubscribe' => $baseDir . '/classes/webservice/WebserviceSpecificManagementSendinblueunsubscribe.php',
);