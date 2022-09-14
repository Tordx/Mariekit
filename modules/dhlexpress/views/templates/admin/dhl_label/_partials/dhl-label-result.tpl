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
  <div id="dhl-label-error" class="alert alert-danger">
    {foreach $description as $text}
      <p>{$text|escape:'html':'utf-8'}</p>
    {/foreach}
  </div>
{else}
  {if isset($alreadyGenerated) && $alreadyGenerated === true}
    <div class="alert alert-warning">
      <p>{l s='You already generated the return label of this shipment.' mod='dhlexpress'}</p>
      <p>{l s='You can download it again using the link below.' mod='dhlexpress'}</p>
      <p>
        {l s='If you want to generate a new one, please delete this label first on' mod='dhlexpress'}
        <a href="{$link->getAdminLink('AdminDhlOrders')|escape:'html':'utf-8'}">{l s ='DHL Orders' mod='dhlexpress'}</a>
      </p>
    </div>
  {/if}
  {if isset($plt) && $plt == 0}
      <div class="alert alert-info">
        {l s='Please print your invoice and insert it inside your package' mod='dhlexpress'}
      </div>
  {/if}
  <div id="dhl-document-download">
    <p>
      <b>{l s='AWB. number: ' mod='dhlexpress'}</b>
      <span id="dhl-label-awbnumber">{$labelDetails['AirwayBillNumber']|escape:'html':'utf-8'}</span>
    </p>
    <p>
      <b>{l s='DHL product chosen: ' mod='dhlexpress'}</b>
      <span id="dhl-label-product-chosen">{$labelDetails['ProductShortName']|escape:'html':'utf-8'}</span>
    </p>
    <div class="dhl-picto-div">
      {if !$freeLabel}
        <p>
          <a class="dhl-download-label-link"
             target="_blank"
             href="{$link->getAdminLink('AdminDhlLabel')|escape:'html':'utf-8'}&ajax=1&action=downloadlabel&id_dhl_label={$id_dhl_label|intval}"
          >
            {l s='Download the label' mod='dhlexpress'}
          </a>
        </p>
        {if isset($id_dhl_invoice) && $id_dhl_invoice}
          <p>
            <a class="dhl-download-label-link"
               target="_blank"
               href="{$link->getAdminLink('AdminDhlCommercialInvoice')|escape:'html':'utf-8'}&ajax=1&action=downloadinvoice&id_dhl_label={$id_dhl_label|intval}"
            >
              {l s='Download the invoice' mod='dhlexpress'}
            </a>
          </p>
        {/if}
      {else}
        <form id="download-free-label" class="dhl-download-free-label" action="{$link->getAdminLink('AdminDhlLabel')|escape:'html':'utf-8'}&action=downloadFreeLabel" method="post" enctype="multipart/form-data">
          <input type="hidden" name="dhl_free_label_filetype" value="{$labelDetails['LabelImage']['OutputFormat']|escape:'html':'utf-8'}" />
          <input type="hidden" name="dhl_free_label_base64" value="{$labelDetails['LabelImage']['OutputImage']|escape:'html':'utf-8'}" />
          <input type="hidden" name="dhl_free_label_awbnumber" value="{$labelDetails['AirwayBillNumber']|escape:'html':'utf-8'}" />
          <input id="dhl-free-label-download" class="btn btn-primary" type="submit" name="dhl_free_label_download" value="{l s='Download the label' mod='dhlexpress'}" />
        </form>
      {/if}
      <img class="dhl-picto" src="{$dhl_img_path|escape:'html':'utf-8'}barcode.jpg">
    </div>
  </div>
{/if}
