{**
* @author   Twispay
* @version  1.0.1
*}

<!-- Confirmation template for prestashop versions <= 1.7 -->
{if (isset($status) == true) && ($status == 'ok')}
	<h3>{l s='Your order on %s is complete.' sprintf=$shop_name mod='twispay'}</h3>
	<p>
		<br />- {l s='Amount' mod='twispay'} : <span class="price"><strong>{$total|escape:'htmlall':'UTF-8'}</strong></span>
		<br />- {l s='Reference' mod='twispay'} : <span class="reference"><strong>{$reference|escape:'html':'UTF-8'}</strong></span>
		<br /><br />{l s='An email has been sent with this information.' mod='twispay'}
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='twispay'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team.' mod='twispay'}</a>
	</p>
{else}
	<h3>{l s='Your order on %s has not been accepted.' sprintf=$shop_name mod='twispay'}</h3>
	<p>
		<br />- {l s='Reference' mod='twispay'} <span class="reference"> <strong>{$reference|escape:'html':'UTF-8'}</strong></span>
		<br /><br />{l s='Please, try to order again.' mod='twispay'}
		<br /><br />{l s='If you have questions, comments or concerns, please contact our' mod='twispay'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='expert customer support team.' mod='twispay'}</a>
	</p>
{/if}
<hr />
