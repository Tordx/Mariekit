<?php
/* Smarty version 3.1.43, created on 2022-10-11 10:27:52
  from 'C:\xampp\htdocs\prestashop\themes\Mariekit-canada\templates\_partials\helpers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63457d6854e8e7_67861988',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'aa1ff9dcd79653ea8c611117e4a59d57718c3e84' => 
    array (
      0 => 'C:\\xampp\\htdocs\\prestashop\\themes\\Mariekit-canada\\templates\\_partials\\helpers.tpl',
      1 => 1661419595,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_63457d6854e8e7_67861988 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'renderLogo' => 
  array (
    'compiled_filepath' => 'C:\\xampp\\htdocs\\prestashop\\var\\cache\\prod\\smarty\\compile\\Mariekit_canadalayouts_layout_left_column_tpl\\aa\\1f\\f9\\aa1ff9dcd79653ea8c611117e4a59d57718c3e84_2.file.helpers.tpl.php',
    'uid' => 'aa1ff9dcd79653ea8c611117e4a59d57718c3e84',
    'call_name' => 'smarty_template_function_renderLogo_205442476663457d6847ec43_36834454',
  ),
));
?> 

<?php }
/* smarty_template_function_renderLogo_205442476663457d6847ec43_36834454 */
if (!function_exists('smarty_template_function_renderLogo_205442476663457d6847ec43_36834454')) {
function smarty_template_function_renderLogo_205442476663457d6847ec43_36834454(Smarty_Internal_Template $_smarty_tpl,$params) {
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
?>

  <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['urls']->value['pages']['index'], ENT_QUOTES, 'UTF-8');?>
">
    <img
      class="logo img-fluid"
      src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['src'], ENT_QUOTES, 'UTF-8');?>
"
      alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8');?>
"
      width="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['width'], ENT_QUOTES, 'UTF-8');?>
"
      height="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo_details']['height'], ENT_QUOTES, 'UTF-8');?>
">
  </a>
<?php
}}
/*/ smarty_template_function_renderLogo_205442476663457d6847ec43_36834454 */
}
