<?php
/* Smarty version 3.1.43, created on 2022-10-11 10:27:40
  from 'C:\xampp\htdocs\prestashop\modules\ps_checkout\views\templates\hook\displayProductAdditionalInfo.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63457d5ce99ee2_02213540',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9cfca9f22e6c43d818facc245f6e37ca20af9c03' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\modules\\ps_checkout\\views\\templates\\hook\\displayProductAdditionalInfo.tpl',
      1 => 1662527701,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_63457d5ce99ee2_02213540 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ps_checkout payment-method-logo-block left">
  <div class="ps_checkout payment-method-logo-block-title">
    <img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['modulePath']->value, ENT_QUOTES, 'UTF-8');?>
views/img/lock_checkout.svg" alt="">
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'100% secure payments','mod'=>'ps_checkout'),$_smarty_tpl ) );?>

  </div>
  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['paymentOptions']->value, 'paymentOption');
$_smarty_tpl->tpl_vars['paymentOption']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['paymentOption']->value) {
$_smarty_tpl->tpl_vars['paymentOption']->do_else = false;
?>
    <?php if ($_smarty_tpl->tpl_vars['paymentOption']->value == 'card') {?>
      <div class="ps_checkout payment-method-logo w-fixed">
        <div class="wrapper"><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['modulePath']->value, ENT_QUOTES, 'UTF-8');?>
views/img/visa.svg" alt=""></div>
      </div>
      <div class="ps_checkout payment-method-logo w-fixed">
        <div class="wrapper"><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['modulePath']->value, ENT_QUOTES, 'UTF-8');?>
views/img/mastercard.svg" alt=""></div>
      </div>
      <div class="ps_checkout payment-method-logo w-fixed">
        <div class="wrapper"><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['modulePath']->value, ENT_QUOTES, 'UTF-8');?>
views/img/amex.svg" alt=""></div>
      </div>
    <?php } else { ?>
      <div class="ps_checkout payment-method-logo w-fixed">
          <div class="wrapper"><img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['modulePath']->value, ENT_QUOTES, 'UTF-8');?>
views/img/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['paymentOption']->value, ENT_QUOTES, 'UTF-8');?>
.svg" alt=""></div>
      </div>
    <?php }?>
  <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
