# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

### [Unreleased]

### [1.2.9] - 2021-09-22
##### Fixed
- Adjust getProductLink argument to accommodate Friendly urls in API response

### [1.2.8] - 2021-07-29
##### Fixed
- Remove use of getContextType alias for <1.7.2 compatibility.

### [1.2.7] - 2021-06-09
##### Fixed
- Use addEventListener for "Viewed Product" tracking setup to support multiple callbacks.

### [1.2.6] - 2021-05-10
##### Fixed
- Throw WebserviceException on json_encode failure in getContent method.

### [1.2.5] - 2021-05-06
##### Added
- Email consent type for subscriptions.
- Sync birthday from account create/update.

##### Fixed
- Verify controller page_name property exists for custom checkout identification method.
- Use addEventListener on email field to support multiple callbacks.

### [1.2.4] - 2021-03-19
##### Added
- Support Started Checkout events on 'The Checkout' (one page checkout) module.

##### Fixed
- Respect SSL in Started Checkout ajax request.

### [1.2.3] - 2021-03-12
##### Added
- Support Started Checkout events on KnowBand's SuperCheckout (one page checkout) module.

##### Fixed
- Handle non-existent order IDs in OrderQueryService.
- Remove abstract definition in PayloadServiceInterface for PHP 5.X compatibility.

### [1.2.2] - 2021-01-07
##### Changed
- Use internal started checkout statistic name.

### [1.2.1] - 2020-12-24
##### Changed
- Handle default order status mapping.

### [1.2.0] - 2020-12-18
##### Added
- Add tab and admin controller for module configuration.
- Add order status mapping option in module configuration.
- Add mapped order status to API order payload.
- Add order_states/map endpoint.

##### Changed
- Do not create new webservice key if we've already created one previously.
- Unregister hooks on uninstall.

### [1.1.1] - 2020-12-03
##### Added
- Add Added to Cart event.
- Add parent controller for ajax routes.

##### Changed
- Refactor building line items for better reusability.

### [1.1.0] - 2020-11-17
##### Added
- Add tags to Order Payload line items.
- Add tags to Started Checkout line items and top level.
- Cookie user's email in checkout if not logged in.

##### Changed
- Utilize separate JS files instead of template for onsite javascript.

##### Fixed
- Return image path when building product image URLs for ssl enabled stores.

### [1.0.3] - 2020-11-03
##### Added
- Utils class with product image link creation method.
- Add image_url property to order line items.
- Add cart rules codes array to order payload.
- Display account signup link in config page if api keys are not set.

##### Changed
- Updated autoloader with Utils class.
- Use Utils image link method in buildReclaim, remove old method definition.
- Refactor buildReclaim cart discount total calculation.
- Change contact email address in file headers.

### [1.0.2] - 2020-10-23
##### Added
- Checkbox option for syncing subscribers to Klaviyo list.
- Help text for API key config form input.
- Add total discount amount and item count properties to Started Checkout events.

##### Changed
- Cast cursor pagination predicate using bqSQL method.
- Escape vars in smarty templates.
- Update README.md with instructions for updating module and new manual install instructions.

##### Fixed
- Return unique categories array in Started Checkout event data.

### [1.0.1] - 2020-10-21
##### Added
- Add UTC timestamps to order payload.

##### Changed
- Use config value to convert timezone on queries to klaviyo resource.
- Handle injecting started checkout js for logged-in users.
- Use variant images for Started Checkout event line items.

##### Fixed
- Display saved Klaviyo config values with multi-shop disabled.

### [1.0.0] - 2020-10-08
##### Added
- Initial release accepted by PrestaShop.

[Unreleased]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.9...HEAD
[1.2.9]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.8...1.2.9
[1.2.8]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.7...1.2.8
[1.2.7]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.6...1.2.7
[1.2.6]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.5...1.2.6
[1.2.5]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.4...1.2.5
[1.2.4]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.3...1.2.4
[1.2.3]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.0.3...1.1.0
[1.0.3]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/klaviyo/prestashop_klaviyo/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/klaviyo/prestashop_klaviyo/releases/tag/1.0.0
