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
<div class="DG-box">
    {foreach $dhl_extracharges as $extracharge}
      <div class="form-group
      {if $extracharge.id_dhl_extracharge == $extracharge_insurance} dhl-label-extracharge-insurance dhl-extracharge-non-doc
      {elseif $extracharge.id_dhl_extracharge == $extracharge_excepted} dhl-label-extracharge-excepted dhl-extracharge-non-doc
      {elseif $extracharge.id_dhl_extracharge == $extracharge_liability} dhl-label-extracharge-liability
      {elseif $extracharge.id_dhl_extracharge == $extracharge_dangerous} dhl-label-extracharge-dangerous-goods
      {else}dhl-extracharge-non-doc{/if}
      ">

        {if $extracharge.id_dhl_extracharge == $extracharge_dangerous}
        <p class="dhl-dangerous-extracharges">
          <i class="icon icon-warning"></i>
          <span>{l s='Below are extracharges for dangerous shipments. You need to have validation of DHL before using them. For that, please contact your commercial' mod='dhlexpress'}</span>
        </p>
        {/if}
        <label class="control-label col-lg-3">
                  <span class="label-tooltip"
                        data-toggle="tooltip"
                        data-html="true"
                        title=""
                        data-original-title="{$extracharge.description|escape:'html':'utf-8'}"
                  >
                    {$extracharge.name|escape:'html':'utf-8'}
                  </span>
        </label>
        <div class="col-lg-2 {if $extracharge.id_dhl_extracharge != $extracharge_insurance && $extracharge.id_dhl_extracharge != $extracharge_DTP && $extracharge.extracharge_code != 'IB' }btn_DG {/if}">
                  <span class="switch prestashop-switch fixed-width-lg">
                    <input
                      {if $extracharge.id_dhl_extracharge == $extracharge_insurance}class="dhl-label-extracharge-insurance-on"
                      {elseif $extracharge.id_dhl_extracharge == $extracharge_excepted}class="dhl-label-extracharge-excepted-on"
                      {elseif $extracharge.id_dhl_extracharge == $extracharge_liability}class="dhl-label-extracharge-liability-on"{/if}
                      type="radio"
                      name="extracharge_{$extracharge.id_dhl_extracharge|intval}"
                      id="extracharge-{$extracharge.id_dhl_extracharge|intval}_on"
                      value="1"
                      {if $extracharge.active}checked="checked"{/if}>
                    <label for="extracharge-{$extracharge.id_dhl_extracharge|intval}_on">{l s='Yes' mod='dhlexpress'}</label>
                    <input
                      {if $extracharge.id_dhl_extracharge == $extracharge_insurance}class="dhl-label-extracharge-insurance-off"
                      {elseif $extracharge.id_dhl_extracharge == $extracharge_excepted}class="dhl-label-extracharge-excepted-off"
                      {elseif $extracharge.id_dhl_extracharge == $extracharge_excepted}class="dhl-label-extracharge-liability-off"{/if}
                      type="radio"
                      name="extracharge_{$extracharge.id_dhl_extracharge|intval}"
                      id="extracharge-{$extracharge.id_dhl_extracharge|intval}_off"
                      value="0"
                      {if !$extracharge.active}checked="checked"{/if}>
                    <label for="extracharge-{$extracharge.id_dhl_extracharge|intval}_off">{l s='No' mod='dhlexpress'}</label>
                    <a class="slide-button btn"></a>
                  </span>
        </div>
        <div class="form-group div_nbr_pices_concerned dhl_nbr_pieces_concerned_{$extracharge.id_dhl_extracharge} col-lg-3" style="display: none;">
            <label class="control-label" for="dhl-nbr_piced_concerned">
              {l s='Nb colis' mod='dhlexpress'}
            </label>
            <div class="input-group fixed-width-xs dhl-total-pieces-concerned-div">
                <input  oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" 
                        name="dhl-number-pieces-concerned-{$extracharge.id_dhl_extracharge|intval}"
                        type="text"
                        value=""
                        class="input fixed-width-sm dhl-number-pieces-concerned">
                <span class="input-group-addon dhl-total-pieces-concerned" id="dhl-total-pieces-concerned_{$extracharge.id_dhl_extracharge|intval}" name="dhl-total-pieces-concerned"> / 0 </span>
            </div>
        </div>
       {if $extracharge.id_dhl_extracharge == $extracharge_excepted}
            <div class="form-group col-lg-4" id="type_designation_div">
                <label class="control-label un_label" for="dhl-label-reference-id">
                  {l s='UN' mod='dhlexpress'}
                </label>

                <div class="col-lg-8">
                  <div class="input-group">
                    <span id="dhl_type_designation_id_counter" class="input-group-addon">35</span>
                    <input type="text"
                           name="TYPE_DESIGNATION_UN_XXXX"
                           id="TYPE_DESIGNATION_UN_XXXX"
                           value="{$type_designation|escape:'html':'utf-8'}"
                           data-maxchar="35"
                           maxlength="35"
                    >
                  </div>
                  <script type="text/javascript">
                    $(document).ready(function () {
                      countDown($("#TYPE_DESIGNATION_UN_XXXX"), $("#dhl_type_designation_id_counter"));
                    });
                  </script>
                </div>
              </div>
        {/if}
      </div>

    {/foreach}
     <input type="hidden" name="dhl-total-pieces-concerned2" id="dhl-total-pieces-concerned2" value="" />
 </div>