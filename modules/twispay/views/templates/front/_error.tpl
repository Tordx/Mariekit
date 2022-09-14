{**
* @author   Twispay
* @version  1.0.1
*}

<div>
  <h3>{l s='An error occurred' mod='twispay'}:</h3>
  <ul class="alert alert-danger">
    <li>{l s='The payment could not be processed. Please ' mod='twispay'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='contact' mod='twispay'}</a> {l s=' our expert customer support team.' mod='twispay'}</li>
  </ul>
</div>
