<?php
/* Smarty version 3.1.43, created on 2022-10-10 16:51:08
  from 'C:\xampp\htdocs\prestashop\themes\Mariekit-canada\templates\catalog\_partials\product-flags.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_634485bcb45bd5_44130436',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d669f8c51cd60b843fc9fa9e4ae252a746f8779' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\themes\\Mariekit-canada\\templates\\catalog\\_partials\\product-flags.tpl',
      1 => 1661419593,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_634485bcb45bd5_44130436 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->compiled->nocache_hash = '1354844335634485bcb42a93_87914609';
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2008895932634485bcb43c46_19967494', 'product_flags');
?>

<?php }
/* {block 'product_flags'} */
class Block_2008895932634485bcb43c46_19967494 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_flags' => 
  array (
    0 => 'Block_2008895932634485bcb43c46_19967494',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

    <ul class="product-flags js-product-flags">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['flags'], 'flag');
$_smarty_tpl->tpl_vars['flag']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['flag']->value) {
$_smarty_tpl->tpl_vars['flag']->do_else = false;
?>
            <li class="product-flag <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['flag']->value['label'], ENT_QUOTES, 'UTF-8');?>
</li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
<?php
}
}
/* {/block 'product_flags'} */
}