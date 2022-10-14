<?php
/* Smarty version 3.1.43, created on 2022-10-13 14:11:45
  from 'C:\xampp\htdocs\prestashop\modules\tidiolivechat\views\templates\front\widget.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_634854e1db9165_64372431',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e018fde6d6ed1a0a7633bcf7bdfd184d53cd2357' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\modules\\tidiolivechat\\views\\templates\\front\\widget.tpl',
      1 => 1662727620,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_634854e1db9165_64372431 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['widgetUrl']->value) {?>
    <?php echo '<script'; ?>
 src="<?php echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['widgetUrl']->value,'javascript','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" async><?php echo '</script'; ?>
>
<?php }
}
}
