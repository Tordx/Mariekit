# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.5.4] - 2022-08-30
### Fixed
- Fix PLT use invoice params

## [2.5.3] - 2022-07-29
### Fixed
- Fix PLT with multiple products

## [2.5.2] - 2022-07-08
### Fixed
- Fix PLT & Return label

## [2.5.1] - 2022-07-05
### Fixed
- HS Code is not required

## [2.5.0] - 2022-06-30
### Added
- XML Services v10

### Changed
- Update PLT eligibility data
- Update credentials

## [2.4.9] - 2020-03-13
### Added
- Allow PLT on the ESI offer (Economy DHL)
- Adding UK into Economy DHL offer

### Changed
- Standardize access for customers on the DHL module

## [2.4.8] - 2020-03-05
### Added
- Northern Ireland is considered to be in the European Union for shipments to GB

### Changed

- Add EORI column in DHL Address table
- Add VAT GB column in DHL Address table
- Change PickupTypeCode value for pickup depending on the date
- Update tracking url

### Fixed

- Fix error when extrachrage is not a dangerous code
- Fix error when dhlCapital table does not exist
- Fix jquery error by appdating jquery live() by on()

## [2.4.7] - 2020-12-21
### Added
- Add capitals to handle front quotation
- Choice between order ID or reference for listings

### Changed

- Handle Brexit as of 01/01/2021
- Change PickupTypeCode value for pickup
- Update XMLPI version
- Handle 1.7.7 admin order
- Update dangerous codes

### Fixed

- Fix empty extracharges

## [2.4.6] - 2020-06-24
### Changed

- Add ID order column in DHL Orders table

### Fixed

- Fix 500 error while generating bulk labels
- Fix zip download

## [2.4.4] - 2020-03-05
### Changed

- Replace DB cache with static cache

### Fixed

- Fix credentials check

## [2.4.3] - 2019-06-26
### Changed

- Update PLT max amounts per country

### Fixed

- Add missing JS function in PrestaShop 1.6.1.0 & 1.6.1.1
- Add missing format label detail
- Fix error when requesting a quotation in BO
- Fix error when filtering in BO pages

## [2.4.0] - 2019-02-22
### Added

- Add PLT feature

## [2.3.5] - 2018-12-13
### Fixed

- Fix undefined property in DHL Manifest
- Better response management when DHL system is down
- Fix cache value field type

## [2.3.4] - 2018-07-23
### Added

- Process shipment on non-DHL orders from AdminOrders page
- Allow multiple LocalProductCode for a same GlobalProductCode

### Fixed

- Fix rounds & casts

## [2.3.3] - 2018-04-26
### Added

- Include LocalProductCode in label generation (single label only)

### Changed

- Improve FO performance on PrestaShop 1.7

### Fixed

- Fix credentials used when making quotation in FO

## [2.3.2] - 2018-04-19
### Fixed

- Fix case where no DHL products is shown whereas it should be

## [2.3.1] - 2018-03-08
### Changed

- Replace log filename with full URL

### Fixed

- Fix logical process for declared value
- Fix BO quotation issue if no currency in last product
- Fix broken links on free label download action

## [2.3.0] - 2018-01-26
### Added

- New menu that lets merchants generate bulk label
- Allow free delivery by zone from a certain amount using real-time DHL prices
- Option to include archive doc copy in label files

### Changed

- New column in Manifest label list

### Fixed

- Fix cast on weights, dimensions and prices
- Delete spaces around account number when saving caused by copy-paste
- Fix bug when there's more than 99 quantities in a cart
- Fix error when email address is longer than 50 chars
- Fix special chars bug on free label generation
- Fix occasional download label error when module directory is not writable

## [2.2.0] - 2017-11-23
### Fixed

- Fix error on the tracking cron
- Fix bulk tracking when more than 10 shipments

### Added

- Add logging feature on tracking, quotation, label operations and new order creation

## [2.1.6] - 2017-11-02
### Fixed

- Missing require in tracking cron task

### Changed

- Cron URL giving for tracking update

## [2.1.5] - 2017-10-24
### Fixed

- Handle multiple display of DHL Orders
- Missing cast in SQL queries

## [2.1.4] - 2017-10-19
### Changed

- Store label format along with the label
- Update the way merchants download labels

### Fixed

- Typo for French language (DHL Pickup)
- Label format identifier when generating a label
