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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<form action="{$action|escape:'htmlall':'UTF-8'}" id="payment-form" style="margin:0 2.5em">
    <label for="apcpm">{l s='Choose A Payment Method' mod='astropaypayments'}:</label>
    {foreach from=$payment_methods item='method'}
        <br/>
        <input type="radio" name="apcpm" value="{$method['code']|escape:'htmlall':'UTF-8'}"/>
        <img class="payment_logo" src="{$img_base|escape:'htmlall':'UTF-8'}/{$method['code']|escape:'htmlall':'UTF-8'}_AP.png" alt="{$method['code']|escape:'htmlall':'UTF-8'}" style="width:32px;height:auto;margin:0 3px"/>
        {$all_methods[$method['code']]|escape:'htmlall':'UTF-8'}
    {/foreach}
</form>