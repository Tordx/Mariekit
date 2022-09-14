{**
* @author   Twispay
* @version  1.0.1
*}

{if $action && $inputs}
  <form accept-charset="UTF-8" id="twispay_payment_form" action="{$action|escape:'quotes'}" method="POST">
    <input type="hidden" name="jsonRequest" value="{$inputs['jsonRequest']|escape:'htmlall':'UTF-8'}" />
    <input type="hidden" name="checksum" value="{$inputs['checksum']|escape:'htmlall':'UTF-8'}" />
  </form>
{/if}
