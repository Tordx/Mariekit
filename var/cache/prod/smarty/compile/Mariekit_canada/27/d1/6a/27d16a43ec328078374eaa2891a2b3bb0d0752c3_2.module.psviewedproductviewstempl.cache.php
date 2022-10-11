<?php
/* Smarty version 3.1.43, created on 2022-10-11 10:27:59
  from 'module:psviewedproductviewstempl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63457d6f9aa265_76508275',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '27d16a43ec328078374eaa2891a2b3bb0d0752c3' => 
    array (
      0 => 'module:psviewedproductviewstempl',
      1 => 1661419599,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
    'file:catalog/_partials/productlist.tpl' => 1,
  ),
),false)) {
function content_63457d6f9aa265_76508275 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '167486530563457d6f980462_50164443';
?>
<section class="featured-products clearfix mt-3">
  <h2><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Viewed products','d'=>'Shop.Theme.Catalog'),$_smarty_tpl ) );?>
</h2>
  <?php $_smarty_tpl->_subTemplateRender("file:catalog/_partials/productlist.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 9999, $_smarty_tpl->cache_lifetime, array('products'=>$_smarty_tpl->tpl_vars['products']->value,'productClass'=>"col-xs-6 col-lg-4 col-xl-3"), 0, false);
?>
</section>
<?php }
}
