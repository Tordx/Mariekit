<?php
/* Smarty version 3.1.43, created on 2022-09-15 09:09:29
  from 'C:\xampp\htdocs\prestashop\modules\crazyelements\views\templates\front\button.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63232409183349_61547539',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8fa14332a951dcb97357d67f13356ed336a6f7d4' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\modules\\crazyelements\\views\\templates\\front\\button.tpl',
      1 => 1662386756,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_63232409183349_61547539 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/html" id="edit_with_button">
	<a href="<?php echo $_smarty_tpl->tpl_vars['proper_href']->value;?>
" id="edit_with_button_link" class="button button-primary button-hero"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="Crazy Elements Logo"> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Edit with Crazyelements','mod'=>'crazyelements'),$_smarty_tpl ) );?>
</a>
<?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/html" id="edit_catg_with_crazy">
		<a href="https://classydevs.com/prestashop-page-builder/pricing/?utm_source=crazyfree_catg_desc&utm_medium=crazyfree_catg_desc&utm_campaign=crazyfree_catg_desc&utm_id=crazyfree_catg_desc&utm_term=crazyfree_catg_desc&utm_content=crazyfree_catg_desc"  id="edit_catg_with_crazy_link" class="button button-primary button-hero" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['icon_url']->value;?>
" alt="Crazy Elements Logo"> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Get Crazyelements Pro and Edit Your Category Description More Beautifully','d'=>'Modules.Crazyelements.Addjs'),$_smarty_tpl ) );?>
</a>
<?php echo '</script'; ?>
><?php }
}
