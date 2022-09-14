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

<div id="btn_view_invoice" class="btn-group  btn_view_invoice" style="display: none;text-transform: none;">
  <a target="_blank"
     href="{$link->getAdminLink('AdminDhlCommercialInvoice')|escape:'html':'utf-8'}&ajax=1&action=downloadInvoicePageLabel&file={$file}"
     title="View invoice"
     class="edit btn btn-default btn-primary"
     style="text-transform: none; border-right: 2px solid #2eacce;">
    <i class="icon-eye"></i> {l s='View invoice' mod='dhlexpress'}
  </a>
  <button class="btn btn-default dropdown-toggle btn-primary" data-toggle="dropdown">
    <i class="icon-caret-down"></i>&nbsp;
  </button>
  <ul class="dropdown-menu">
    <li>
      <a class="edit dhl-edit-invoice" id="dhl-edit-invoice"
         onclick="displayFormInvoiceUpdate();">
        <i class="icon-edit"></i> {l s='Edit invoice' mod='dhlexpress'}
      </a>
    </li>
  </ul>
</div>
<input type="hidden" id="base64_decode" name="base64_decode" value="{$base64_decode}"/>
<input type="hidden" id="file_path" name="file_path" value="{$file}"/>

<script type="text/javascript">
  {literal}
  $('#dhl-edit-invoice').click(function (e) {
      var tokenDhlCI = '{/literal}{getAdminToken tab='AdminDhlCommercialInvoice'}{literal}';
      var data = {
          controller: 'AdminDhlCommercialInvoice',
          ajax: 1,
          action: 'updateTmp',
          token: tokenDhlCI,
      };

      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: baseAdminDir + 'index.php?' + $.param(data),
          data: $('#create-label').serialize(),
          success: function (data) {
              alert(data);
          }
      });
  });
  {/literal}
</script>