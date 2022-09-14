{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2021 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $errors}
  <div id="dhl-services-error" class="alert alert-danger">
    {foreach $description as $text}
      <p>{$text|escape:'html':'utf-8'}</p>
    {/foreach}
  </div>
{else}
  <div id="dhl-services-block" class="col-lg-12">
    {if !$free_label}
      <p>{l s='Chosen product by the customer: ' mod='dhlexpress'}
        <strong>{$service_wanted|escape:'html':'utf-8'}</strong>
      </p>
      <p>{l s='Shipping cost paid by the customer (tax excl.): ' mod='dhlexpress'}
        <strong>{$shipping_paid|escape:'html':'utf-8'}</strong> <span
          class="dhl-price-converted">{if isset($convert_price)}({$services_currency|escape:'html':'utf-8'} {$convert_price|string_format:'%.2f'}){/if}</span></p>
    {/if}
    <div class="tab-content panel collapse in">
      <table class="table dhl-services-table">
        <thead>
        <tr>
          <th></th>
          <th><span class="title_box">{l s='Product name' mod='dhlexpress'}</span></th>
          <th><span class="title_box">{l s='Delivery on' mod='dhlexpress'}</span></th>
          <th><span class="title_box">{l s='Pickup cutoff time' mod='dhlexpress'}</span></th>
          <th><span class="title_box">{l s='Price (tax excl.)' mod='dhlexpress'}</span></th>
        </tr>
        </thead>
        <tbody>
          {foreach $services as $code => $servicesLocal}
            {assign var=code_value value=$code|trim}
            {foreach $servicesLocal as $service}
              {if isset($available_services[$code_value])}
                  <tr>
                  <td>
                    <input {if !$free_label && !$return_label} {if $service.GlobalProductCode == "P" || $service.GlobalProductCode == "E" || $service.GlobalProductCode == "Y" || $service.GlobalProductCode == "H" }onclick="displayPltOption({$service_plt});"{else} onclick="hiddePltOption();"{/if} {/if}
                       {if ($service_wanted_code == '' && $servicesLocal@first && $service@first) || ($code|trim == $service_wanted_code)}  class="display_plt_checked global_product_code_{$service.GlobalProductCode}" {*checked="checked"*} {/if} type="radio" name="dhl_label_service" class="dhl_label_service global_product_code_{$service.GlobalProductCode}" value="{$code|trim|escape:'html':'utf-8'}_{$service.LocalProductCode|escape:'html':'utf-8'}" />
                    <input type="hidden" name="dhl_label_local_code_{$code|trim|escape:'html':'utf-8'}_{$service.LocalProductCode|escape:'html':'utf-8'}" value="{$service.LocalProductCode|escape:'html':'utf-8'}" />
                  </td>
                  <td>{$service.ProductShortName|escape:'html':'utf-8'}</td>
                  <td>{$service.DeliveryDate|escape:'html':'utf-8'}</td>
                  <td>{$service.PickupCutoffTime|escape:'html':'utf-8'}</td>
                  <td><strong>{$services_currency|escape:'html':'utf-8'} {$service.ChargeWithoutTax|floatval}</strong></td>
                </tr>
              {/if}
            {/foreach}
          {/foreach}
        </tbody>
      </table>
    </div>
  </div>

{/if}
