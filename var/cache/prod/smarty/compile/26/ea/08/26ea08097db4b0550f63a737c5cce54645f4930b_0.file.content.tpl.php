<?php
/* Smarty version 3.1.43, created on 2022-10-11 09:17:14
  from 'C:\xampp\htdocs\prestashop\admin580d2j0ce\themes\default\template\content.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63456cda1f4d90_39613054',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '26ea08097db4b0550f63a737c5cce54645f4930b' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\admin580d2j0ce\\themes\\default\\template\\content.tpl',
      1 => 1661419503,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_63456cda1f4d90_39613054 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="ajax_confirmation" class="alert alert-success hide"></div>
<div id="ajaxBox" style="display:none"></div>

<div class="row">
	<div class="col-lg-12">
		<?php if ((isset($_smarty_tpl->tpl_vars['content']->value))) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div>
<?php }
}
