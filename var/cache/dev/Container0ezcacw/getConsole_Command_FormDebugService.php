<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the private 'console.command.form_debug' shared service.

$this->services['console.command.form_debug'] = $instance = new \Symfony\Component\Form\Command\DebugCommand(${($_ = isset($this->services['form.registry']) ? $this->services['form.registry'] : $this->load('getForm_RegistryService.php')) && false ?: '_'}, [0 => 'Symfony\\Component\\Form\\Extension\\Core\\Type', 1 => 'PrestaShopBundle\\Form\\Admin\\Type', 2 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Currencies', 3 => 'Symfony\\Bridge\\Doctrine\\Form\\Type', 4 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Webservice', 5 => 'PrestaShopBundle\\Form\\Admin\\Category', 6 => 'PrestaShopBundle\\Form\\Admin\\Feature', 7 => 'PrestaShopBundle\\Form\\Admin\\Product', 8 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Invoices', 9 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance', 10 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\General', 11 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Administration', 12 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Shipping\\Preferences', 13 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\ProductPreferences', 14 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\CustomerPreferences', 15 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderPreferences', 16 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Import', 17 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Delivery', 18 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Localization', 19 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Geolocation', 20 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Payment\\Preferences', 21 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Email', 22 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Translations', 23 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta', 24 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Category', 25 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Employee', 26 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\RequestSql', 27 => 'PrestaShopBundle\\Form\\Admin\\Type\\Common\\Team', 28 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Theme', 29 => 'PrestaShopBundle\\Form\\Admin\\Catalog\\Category', 30 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Backup', 31 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer', 32 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Language', 33 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Tax', 34 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\Contact', 35 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Pages', 36 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Manufacturer', 37 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Address', 38 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\MailTheme', 39 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order', 40 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Supplier', 41 => 'PrestaShopBundle\\Form\\Admin\\Sell\\CatalogPriceRule', 42 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Catalog', 43 => 'PrestaShopBundle\\Form\\Admin\\Sell\\CustomerService', 44 => 'PrestaShopBundle\\Form\\Admin\\CustomerService\\CustomerThread', 45 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\CreditSlip', 46 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Attachment', 47 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Profile', 48 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product', 49 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Basic', 50 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Image', 51 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shortcut', 52 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Category', 53 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock', 54 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shipping', 55 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Pricing', 56 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\SEO', 57 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options', 58 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination', 59 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderStates', 60 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderReturnStates', 61 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Logs', 62 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Locations', 63 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\FeatureFlag', 64 => 'PrestaShop\\Module\\LinkList\\Form\\Type'], [0 => 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FormType', 1 => 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType', 2 => 'PrestaShopBundle\\Form\\Admin\\Type\\EmailType', 3 => 'Symfony\\Component\\Form\\Extension\\Core\\Type\\FileType', 4 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Currencies\\CurrencyType', 5 => 'Symfony\\Bridge\\Doctrine\\Form\\Type\\EntityType', 6 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Webservice\\WebserviceKeyType', 7 => 'PrestaShopBundle\\Form\\Admin\\Type\\DatePickerType', 8 => 'PrestaShopBundle\\Form\\Admin\\Category\\SimpleCategory', 9 => 'PrestaShopBundle\\Form\\Admin\\Type\\ChoiceCategoriesTreeType', 10 => 'PrestaShopBundle\\Form\\Admin\\Type\\TranslateType', 11 => 'PrestaShopBundle\\Form\\Admin\\Feature\\ProductFeature', 12 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductAttachement', 13 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductCombination', 14 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductCustomField', 15 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductInformation', 16 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductOptions', 17 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductPrice', 18 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductQuantity', 19 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductSeo', 20 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductShipping', 21 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductSpecificPrice', 22 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductSupplierCombination', 23 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductVirtual', 24 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductWarehouseCombination', 25 => 'PrestaShopBundle\\Form\\Admin\\Type\\TypeaheadProductCollectionType', 26 => 'PrestaShopBundle\\Form\\Admin\\Type\\TypeaheadProductPackCollectionType', 27 => 'PrestaShopBundle\\Form\\Admin\\Type\\TypeaheadCustomerCollectionType', 28 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductCombinationBulk', 29 => 'PrestaShopBundle\\Form\\Admin\\Product\\ProductCategories', 30 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Invoices\\GenerateByDateType', 31 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Invoices\\GenerateByStatusType', 32 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Invoices\\InvoiceOptionsType', 33 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\SmartyType', 34 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\DebugModeType', 35 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\OptionalFeaturesType', 36 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\CombineCompressCacheType', 37 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\MediaServersType', 38 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\MemcacheServerType', 39 => 'PrestaShopBundle\\Form\\Admin\\AdvancedParameters\\Performance\\CachingType', 40 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\General\\PreferencesType', 41 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\General\\MaintenanceType', 42 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Administration\\GeneralType', 43 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Administration\\UploadQuotaType', 44 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Administration\\NotificationsType', 45 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Shipping\\Preferences\\HandlingType', 46 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Shipping\\Preferences\\CarrierOptionsType', 47 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\ProductPreferences\\GeneralType', 48 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\ProductPreferences\\StockType', 49 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\CustomerPreferences\\GeneralType', 50 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderPreferences\\GeneralType', 51 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderPreferences\\GiftOptionsType', 52 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Import\\ImportType', 53 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\Delivery\\SlipOptionsType', 54 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Localization\\LocalizationConfigurationType', 55 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Localization\\ImportLocalizationPackType', 56 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Localization\\LocalUnitsType', 57 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Localization\\AdvancedConfigurationType', 58 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Geolocation\\GeolocationByIpAddressType', 59 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Geolocation\\GeolocationIpAddressWhitelistType', 60 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Geolocation\\GeolocationOptionsType', 61 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Payment\\Preferences\\PaymentModulePreferencesType', 62 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Email\\EmailConfigurationType', 63 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Email\\SmtpConfigurationType', 64 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Email\\TestEmailSendingType', 65 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Translations\\ModifyTranslationsType', 66 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Translations\\AddUpdateLanguageType', 67 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Translations\\ExportCataloguesType', 68 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Translations\\CopyLanguageType', 69 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta\\SetUpUrlType', 70 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta\\ShopUrlType', 71 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta\\UrlSchemaType', 72 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta\\SEOOptionsType', 73 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\TrafficSeo\\Meta\\MetaType', 74 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Category\\DeleteCategoriesType', 75 => 'PrestaShopBundle\\Form\\Admin\\Type\\YesAndNoChoiceType', 76 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Employee\\EmployeeOptionsType', 77 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\RequestSql\\SqlRequestSettingsType', 78 => 'PrestaShopBundle\\Form\\Admin\\Type\\Common\\Team\\ProfileChoiceType', 79 => 'PrestaShopBundle\\Form\\Admin\\Type\\CountryChoiceType', 80 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Theme\\PageLayoutsCustomizationType', 81 => 'PrestaShopBundle\\Form\\Admin\\Catalog\\Category\\CategoryType', 82 => 'PrestaShopBundle\\Form\\Admin\\Catalog\\Category\\RootCategoryType', 83 => 'PrestaShopBundle\\Form\\Admin\\Type\\CategoryChoiceTreeType', 84 => 'PrestaShopBundle\\Form\\Admin\\Type\\TranslatableType', 85 => 'PrestaShopBundle\\Form\\Admin\\Type\\TranslatableChoiceType', 86 => 'PrestaShopBundle\\Form\\Admin\\Type\\ShopChoiceTreeType', 87 => 'PrestaShopBundle\\Form\\Admin\\Type\\SearchAndResetType', 88 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\RequestSql\\SqlRequestType', 89 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Backup\\BackupOptionsType', 90 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer\\RequiredFieldsType', 91 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer\\TransferGuestAccountType', 92 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer\\CustomerType', 93 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer\\DeleteCustomersType', 94 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Theme\\ImportThemeType', 95 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Theme\\AdaptThemeToRTLLanguagesType', 96 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Language\\LanguageType', 97 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Currencies\\CurrencyExchangeRateType', 98 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Tax\\TaxOptionsType', 99 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Webservice\\WebserviceConfigurationType', 100 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\Contact\\ContactType', 101 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Pages\\CmsPageCategoryType', 102 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Tax\\TaxType', 103 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Manufacturer\\ManufacturerType', 104 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Employee\\EmployeeType', 105 => 'PrestaShopBundle\\Form\\Admin\\Type\\ChangePasswordType', 106 => 'PrestaShopBundle\\Form\\Admin\\Type\\AddonsConnectType', 107 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Pages\\CmsPageType', 108 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Address\\ManufacturerAddressType', 109 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\Theme\\ShopLogosType', 110 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\MailTheme\\GenerateMailsType', 111 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\MailTheme\\MailThemeConfigurationType', 112 => 'PrestaShopBundle\\Form\\Admin\\Type\\IntegerMinMaxFilterType', 113 => 'PrestaShopBundle\\Form\\Admin\\Type\\NumberMinMaxFilterType', 114 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\ChangeOrdersStatusType', 115 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Supplier\\SupplierType', 116 => 'PrestaShopBundle\\Form\\Admin\\Sell\\CatalogPriceRule\\CatalogPriceRuleType', 117 => 'PrestaShopBundle\\Form\\Admin\\Type\\ReductionType', 118 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Customer\\PrivateNoteType', 119 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\InternalNoteType', 120 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\AddOrderCartRuleType', 121 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\AddProductRowType', 122 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\EditProductRowType', 123 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\UpdateOrderStatusType', 124 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\OrderPaymentType', 125 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Catalog\\FeatureType', 126 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\ChangeOrderCurrencyType', 127 => 'PrestaShopBundle\\Form\\Admin\\Improve\\Design\\MailTheme\\TranslateMailsBodyType', 128 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\UpdateOrderShippingType', 129 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Address\\RequiredFieldsAddressType', 130 => 'PrestaShopBundle\\Form\\Admin\\Sell\\CustomerService\\ReplyToCustomerThreadType', 131 => 'PrestaShopBundle\\Form\\Admin\\CustomerService\\CustomerThread\\ForwardCustomerThreadType', 132 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\CreditSlip\\GeneratePdfByDateType', 133 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\CreditSlip\\CreditSlipOptionsType', 134 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\ChangeOrderAddressType', 135 => 'PrestaShopBundle\\Form\\Admin\\Sell\\CustomerService\\OrderMessageType', 136 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\OrderMessageType', 137 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Address\\CustomerAddressType', 138 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Attachment\\AttachmentType', 139 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\CancelProductType', 140 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Order\\CartSummaryType', 141 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Profile\\ProfileType', 142 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\ProductFormType', 143 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\HeaderType', 144 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Basic\\BasicType', 145 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Basic\\FeaturesType', 146 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Basic\\FeatureValueType', 147 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Basic\\ManufacturerType', 148 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Image\\ImageDropzoneType', 149 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shortcut\\ShortcutsType', 150 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shortcut\\PriceShortcutType', 151 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shortcut\\StockShortcutType', 152 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Category\\CategoriesType', 153 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Category\\CategoriesCollectionType', 154 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Category\\ProductCategoryType', 155 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock\\StockType', 156 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock\\QuantityType', 157 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock\\StockOptionsType', 158 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock\\VirtualProductFileType', 159 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Stock\\AvailabilityType', 160 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shipping\\ShippingType', 161 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shipping\\DimensionsType', 162 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Shipping\\DeliveryTimeNotesType', 163 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Pricing\\PricingType', 164 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Pricing\\RetailPriceType', 165 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Pricing\\UnitPriceType', 166 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\SEO\\SEOType', 167 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\SEO\\SerpType', 168 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\SEO\\RedirectOptionType', 169 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\OptionsType', 170 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\VisibilityType', 171 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\ReferencesType', 172 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\CustomizationsType', 173 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\CustomizationFieldType', 174 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\SuppliersType', 175 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Options\\ProductSupplierType', 176 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\FooterType', 177 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination\\CombinationListType', 178 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination\\CombinationItemType', 179 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination\\CombinationFormType', 180 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination\\CombinationStockType', 181 => 'PrestaShopBundle\\Form\\Admin\\Sell\\Product\\Combination\\CombinationPriceImpactType', 182 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderStates\\OrderStateType', 183 => 'PrestaShopBundle\\Form\\Admin\\Configure\\ShopParameters\\OrderReturnStates\\OrderReturnStateType', 184 => 'PrestaShopBundle\\Form\\Admin\\Type\\LogSeverityChoiceType', 185 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\Logs\\LogsByEmailType', 186 => 'PrestaShopBundle\\Form\\Admin\\Type\\UnavailableType', 187 => 'PrestaShopBundle\\Form\\Admin\\Type\\SubmittableInputType', 188 => 'PrestaShopBundle\\Form\\Admin\\Improve\\International\\Locations\\ZoneType', 189 => 'PrestaShopBundle\\Form\\Admin\\Configure\\AdvancedParameters\\FeatureFlag\\FeatureFlagsType', 190 => 'PrestaShop\\Module\\LinkList\\Form\\Type\\LinkBlockType', 191 => 'PrestaShop\\Module\\LinkList\\Form\\Type\\CustomUrlType'], [0 => 'Symfony\\Component\\Form\\Extension\\Core\\Type\\TransformationFailureExtension', 1 => 'Symfony\\Component\\Form\\Extension\\HttpFoundation\\Type\\FormTypeHttpFoundationExtension', 2 => 'Symfony\\Component\\Form\\Extension\\Validator\\Type\\FormTypeValidatorExtension', 3 => 'Symfony\\Component\\Form\\Extension\\Validator\\Type\\RepeatedTypeValidatorExtension', 4 => 'Symfony\\Component\\Form\\Extension\\Validator\\Type\\SubmitTypeValidatorExtension', 5 => 'Symfony\\Component\\Form\\Extension\\Validator\\Type\\UploadValidatorExtension', 6 => 'Symfony\\Component\\Form\\Extension\\Csrf\\Type\\FormTypeCsrfExtension', 7 => 'Symfony\\Component\\Form\\Extension\\DataCollector\\Type\\DataCollectorTypeExtension', 8 => 'PrestaShopBundle\\Form\\Admin\\Extension\\CommaTransformerExtension', 9 => 'PrestaShopBundle\\Form\\Admin\\Type\\CustomMoneyType', 10 => 'PrestaShopBundle\\Form\\Admin\\Type\\ResizableTextType', 11 => 'PrestaShopBundle\\Form\\Admin\\Extension\\HelpTextExtension', 12 => 'PrestaShopBundle\\Form\\Admin\\Extension\\HintTextExtension', 13 => 'PrestaShopBundle\\Form\\Admin\\Extension\\DefaultEmptyDataExtension', 14 => 'PrestaShopBundle\\Form\\Extension\\DataListExtension', 15 => 'PrestaShopBundle\\Form\\Admin\\Extension\\RowAttributesExtension', 16 => 'PrestaShopBundle\\Form\\Admin\\Extension\\ExternalLinkExtension', 17 => 'PrestaShopBundle\\Form\\Admin\\Extension\\AlertExtension', 18 => 'PrestaShopBundle\\Form\\Admin\\Extension\\LabelOptionsExtension', 19 => 'PrestaShopBundle\\Form\\Admin\\Extension\\ColumnsNumberExtension', 20 => 'PrestaShopBundle\\Form\\Admin\\Extension\\MultistoreConfigurationTypeExtension', 21 => 'PrestaShopBundle\\Form\\Admin\\Extension\\MultistoreExtension', 22 => 'PrestaShopBundle\\Form\\Admin\\Extension\\DownloadFileExtension'], [0 => 'Symfony\\Component\\Form\\Extension\\Validator\\ValidatorTypeGuesser', 1 => 'Symfony\\Bridge\\Doctrine\\Form\\DoctrineOrmTypeGuesser']);

$instance->setName('debug:form');

return $instance;
