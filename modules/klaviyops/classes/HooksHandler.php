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

use Configuration;
use CustomerCore;

use KlaviyoPs;

class HooksHandler
{
    /**
     * @var KlaviyoPs
     */
    private $klaviyopsModule;

    /**
     * HooksHandler constructor.
     *
     * @param KlaviyoPs $klaviyopsModule
     */
    public function __construct(KlaviyoPs $klaviyopsModule)
    {
        $this->klaviyoModule = $klaviyopsModule;
    }

    /**
     * Handle actionCustomerAccount hooks. Includes add and update. Subscribe customer
     * to the Klaviyo list selected in module settings if they subscribed, are active
     * and aren't deleted.
     *
     * @param array $params
     */
    public function handleActionCustomerAccount(array $params)
    {
        $customer = $this->getCustomerFromHookParams($params);
        if (
            $customer->newsletter &&
            $customer->active &&
            !$customer->deleted &&
            Configuration::get('KLAVIYO_PRIVATE_API')
        ) {
            $customProperties = $this->getPropertiesFromCustomer($customer);
            $api = new KlaviyoApiWrapper();
            $api->subscribeCustomer($customer->email, $customProperties);
        }
    }

    /**
     * Handle actionNewsletterSubscriptionAfter hook used in the default PrestaShop
     * Newsletter Subscription module.
     *
     * @param array $params
     */
    public function handleActionNewsletterSubscription(array $params)
    {
        if (!$params['error'] && Configuration::get('KLAVIYO_PRIVATE_API')) {
            $api = new KlaviyoApiWrapper();
            $api->subscribeCustomer($params['email']);
        }
    }

    /**
     * Return new Webservice Resource definition to use specific management interface.
     *
     * @param array $resources
     * @return array[]
     */
    public function handleAddWebserviceResources(array $resources)
    {
        return [
            'klaviyo' => [
                'description' => 'Klaviyo custom endpoints',
                'specific_management' => true,
            ]
        ];
    }

    /**
     * Extract Customer object from hook params.
     *
     * @param array $hookParams
     * @return CustomerCore
     */
    private function getCustomerFromHookParams(array $hookParams)
    {
        if (isset($hookParams['customer']) && $hookParams['customer'] instanceof CustomerCore) {
            return $hookParams['customer'];
        }

        if (isset($hookParams['newCustomer']) && $hookParams['newCustomer'] instanceof CustomerCore) {
            return $hookParams['newCustomer'];
        }
    }

    /**
     * Format custom properties for Klaviyo Subscribe request e.g. birthday, first and last name.
     *
     * @param CustomerCore $customer
     * @return array
     */
    private function getPropertiesFromCustomer(CustomerCore $customer)
    {
        $customerPropertiesMap = [
            'birthday' => 'Birthday',
            'firstname' => 'first_name',
            'lastname' => 'last_name',
        ];

        $customerProperties = [];
        foreach ($customerPropertiesMap as $customerProp => $klaviyoProp) {
            if (isset($customer->{$customerProp})) {
                $customerProperties[$klaviyoProp] = $customer->{$customerProp};
            }
        }

        return $customerProperties;
    }
}
