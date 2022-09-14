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

use Klaviyo\Exception\KlaviyoException;
use Klaviyo\Klaviyo;
use Klaviyo\Model\EventModel;
use Klaviyo\Model\ProfileModel;

class KlaviyoApiWrapper
{
    /** @var Klaviyo Client for Klaviyo's Api. */
    protected $client;

    public function __construct()
    {
        $this->client = new Klaviyo(Configuration::get('KLAVIYO_PRIVATE_API'), Configuration::get('KLAVIYO_PUBLIC_API'));
    }

    /**
     * Get all lists for specific Klaviyo account.
     *
     * @return mixed
     */
    public function getLists()
    {
        return $this->client->lists->getLists();
    }

    /**
     * Subscribe email to the Subscriber List selected on configuration page (if selected).
     *
     * @param string $email
     * @throws KlaviyoException
     */
    public function subscribeCustomer($email, $customProperties = [])
    {
        $profile = new ProfileModel(
            array_merge(
                array(
                    '$email' => $email,
                    '$source' => 'PrestaShop',
                    '$consent' => array('email'),
                ),
                $customProperties
            )
        );
        $listId = Configuration::get('KLAVIYO_SUBSCRIBER_LIST');

        if ($listId) {
            $this->client->lists->subscribeMembersToList($listId, array($profile));
        }
    }

    /**
     * Send event to Klaviyo using the Track endpoint.
     *
     * @param array $event
     * @return bool
     */
    public function trackEvent(array $eventConfig)
    {
        return (bool) $this->client->publicAPI->track(new EventModel($eventConfig));
    }
}
