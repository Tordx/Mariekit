<?php
/* Smarty version 3.1.43, created on 2022-10-13 14:17:05
  from 'module:psimagesliderviewstemplat' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.43',
  'unifunc' => 'content_63485621ed73c2_88272119',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6c2108a17c7103b6e203f4f0621d4645b56b0114' => 
    array (
      0 => 'module:psimagesliderviewstemplat',
      1 => 1661419600,
      2 => 'module',
    ),
  ),
  'cache_lifetime' => 31536000,
),true)) {
function content_63485621ed73c2_88272119 (Smarty_Internal_Template $_smarty_tpl) {
?>
  <div id="carousel" data-ride="carousel" class="carousel slide" data-interval="5000" data-wrap="true" data-pause="hover" data-touch="true">
    <ol class="carousel-indicators">
            <li data-target="#carousel" data-slide-to="0" class="active"></li>
            <li data-target="#carousel" data-slide-to="1"></li>
            <li data-target="#carousel" data-slide-to="2"></li>
          </ol>
    <ul class="carousel-inner" role="listbox" aria-label="Carousel container">
              <li class="carousel-item active" role="option" aria-hidden="false">
          <a href="http://192.168.0.191/prestashop/en/content/6-pasabuy">
            <figure>
              <img src="http://localhost/prestashop/modules/ps_imageslider/images/db15520e53fb14d96bee9ed7e0973ea039950299_C4425BCB-91C6-419C-B25F-2D0CCDFBD3B9.jpeg" alt="We offer the safest and convenient pasabuy here in canada" loading="lazy" width="1110" height="340">
                          </figure>
          </a>
        </li>
              <li class="carousel-item " role="option" aria-hidden="true">
          <a href="http://192.168.0.191/prestashop/en/16-collagen">
            <figure>
              <img src="http://localhost/prestashop/modules/ps_imageslider/images/29a0615324bb6b93a425c84d060b4a3a95c3060e_Untitled-1.png" alt="" loading="lazy" width="1110" height="340">
                          </figure>
          </a>
        </li>
              <li class="carousel-item " role="option" aria-hidden="true">
          <a href="http://localhost/prestashop/en/16-collagen">
            <figure>
              <img src="http://localhost/prestashop/modules/ps_imageslider/images/249dbd6989f99bb081ccce63b7e41c4d15717fa6_Banner.png" alt="" loading="lazy" width="1110" height="340">
                          </figure>
          </a>
        </li>
          </ul>
    <div class="direction" aria-label="Carousel buttons">
      <a class="left carousel-control" href="#carousel" role="button" data-slide="prev" aria-label="Previous">
        <span class="icon-prev hidden-xs" aria-hidden="true">
          <i class="material-icons">&#xE5CB;</i>
        </span>
      </a>
      <a class="right carousel-control" href="#carousel" role="button" data-slide="next" aria-label="Next">
        <span class="icon-next" aria-hidden="true">
          <i class="material-icons">&#xE5CC;</i>
        </span>
      </a>
    </div>
  </div>
<?php }
}
