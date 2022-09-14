{**
* @author   Twispay
* @version  1.0.1
*}

<div id="twispay_order_info" class="panel">
  <div class="panel-heading"><i class="icon-credit-card"></i> {l s='Twispay' mod='twispay'}</div>
  <div class="tab-content">
    <table class="table">
      <tr>
        <td>
          <strong>{l s='Payment status:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.status|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Payment amount:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.amount|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Payment currency:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.currency|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Twispay order ID:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.orderId|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Twispay transaction ID:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.transactionId|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Twispay customer ID:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.customerId|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Twispay transaction kind:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.transactionKind|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Twispay card ID:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.cardId|escape:'html':'utf-8'}
        </td>
      </tr>
      <tr>
        <td>
          <strong>{l s='Transaction date:' mod='twispay'}</strong>
        </td>
        <td>
          {$data.date|escape:'html':'utf-8'}
        </td>
      </tr>
    </table>
  </div>
</div>
