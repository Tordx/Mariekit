<?php
/* Smarty version 3.1.43, created on 2022-10-11 10:27:42
  from 'C:\xampp\htdocs\prestashop\themes\Mariekit-canada\templates\catalog\_partials\product-additional-info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63457d5e249628_86609315',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '537cbf5bf018a7541a871753e46db314aff1c5ee' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\themes\\Mariekit-canada\\templates\\catalog\\_partials\\product-additional-info.tpl',
      1 => 1661419593,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_63457d5e249628_86609315 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="product-additional-info js-product-additional-info">
  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductAdditionalInfo','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

</div>
<?php }
}
