{**
* @author   Twispay
* @version  1.0.1
*}

<!-- Error template for prestashop versions <= 1.7 -->
{extends file='page.tpl'}
{block name="page_content"}
	<div>
		<h3>{l s='An error occurred' mod='twispay'}:</h3>
		<ul class="alert alert-danger">
			{foreach from=$errors item='error'}
				<li>{$error|escape:'htmlall':'UTF-8'}.</li>
			{/foreach}
		</ul>
	</div>
{/block}
