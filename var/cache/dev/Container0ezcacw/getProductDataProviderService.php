<?php

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.
// Returns the public 'PrestaShop\Module\PsEventbus\Provider\ProductDataProvider' shared service.

return $this->services['PrestaShop\\Module\\PsEventbus\\Provider\\ProductDataProvider'] = new \PrestaShop\Module\PsEventbus\Provider\ProductDataProvider(${($_ = isset($this->services['PrestaShop\\Module\\PsEventbus\\Repository\\ProductRepository']) ? $this->services['PrestaShop\\Module\\PsEventbus\\Repository\\ProductRepository'] : $this->load('getProductRepository2Service.php')) && false ?: '_'}, ${($_ = isset($this->services['PrestaShop\\Module\\PsEventbus\\Decorator\\ProductDecorator']) ? $this->services['PrestaShop\\Module\\PsEventbus\\Decorator\\ProductDecorator'] : $this->load('getProductDecoratorService.php')) && false ?: '_'}, ${($_ = isset($this->services['PrestaShop\\Module\\PsEventbus\\Repository\\LanguageRepository']) ? $this->services['PrestaShop\\Module\\PsEventbus\\Repository\\LanguageRepository'] : ($this->services['PrestaShop\\Module\\PsEventbus\\Repository\\LanguageRepository'] = new \PrestaShop\Module\PsEventbus\Repository\LanguageRepository())) && false ?: '_'}, ${($_ = isset($this->services['PrestaShop\\Module\\PsEventbus\\Repository\\BundleRepository']) ? $this->services['PrestaShop\\Module\\PsEventbus\\Repository\\BundleRepository'] : $this->load('getBundleRepositoryService.php')) && false ?: '_'});
