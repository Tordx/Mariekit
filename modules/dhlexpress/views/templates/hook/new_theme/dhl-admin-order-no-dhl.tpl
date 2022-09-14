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

<div class="dhl-admin-orders-new-theme">
  <div class="card mt-2">
      <h3 class="card-header">{l s='DHL Shipping' mod='dhlexpress'}</h3>
      <div class="card-body">
           <img class="dhl-logo" src="{$dhl_img_path|escape:'htmlall':'UTF-8'}dhl.png"/>
      <div class="alert alert-info">
        <p>
          {l s='This order is not associated with DHL carrier. However you can still process this shipment using DHL.' mod='dhlexpress'}
          <br/>
          {l s='Please choose a service then validate.' mod='dhlexpress'}
        </p>
      </div>
      <div class="card-body">
        <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="form-control-label col-lg-3">{l s='Available services' mod='dhlexpress'}</label>
            <div class="col-lg-9">
              <select class="col-lg-4" name="dhl_service_to_associate" id="dhl-service-to-associate">
                {foreach $services_list as $service}
                  <option value="{$service.id_dhl_service|intval}">{$service.name|escape:'htmlall':'utf-8'}</option>
                {/foreach}
              </select>
            </div>
          </div>
          <button type="submit" name="submitAssociateDhlOrder" class="col-lg-offset-3 btn btn-primary">
            {l s='Asociate this order to DHL' mod='dhlexpress'}
          </button>
        </form>
      </div>
      </div>
  </div>
</div>
