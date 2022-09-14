/*
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2021 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

function dhlDeleteRow(e, t)
{
  e.preventDefault();
  $(e.target).closest('tr').remove();
  updateTotalPicesDangerousGoods();
}

function toggleInsuredDeclaredValues()
{
  if ($('.dhl-label-extracharge-insurance-on').prop('checked')) {
    $('.form-group-insured-value').show(400);
  } else {
    $('.form-group-insured-value').hide(200);
  }
}

function toggleExtracharges()
{
  if ($('#sending-doc_on').prop('checked')) {
    $('.dhl-extracharge-non-doc').hide(200);
    $('.form-group-declared-value').hide(200);
    $('.form-group-insured-value').hide(200);
    $('.form-group-excepted').hide(200);
    $('.dhl-label-extracharge-liability').show(400);
  } else {
    $('.dhl-extracharge-non-doc').show(400);
    $('.form-group-declared-value').show(400);
    $('.dhl-label-extracharge-liability').hide(400);
    toggleInsuredDeclaredValues();
  }
}

function countDown($source, $target)
{
    var max = $source.attr("data-maxchar");
    $target.html(max-$source.val().length);

    $source.keyup(function(){
        $target.html(max-$source.val().length);
    });
}

$(document).ready(function () {

  toggleExtracharges();
  toggleInsuredDeclaredValues();
  toggleExtrachargesDangerousGoods();
  toggleExceptedQuantities();
  
  $('.panel_form_invoice').hide(200);

  $('#dhl-sender-address').change(function () {
    var id = $(this).find('option:selected').attr('value');
    $('.dhl-sender-addresses').hide(200);
    $('#dhl-sender-address-' + id).show(400);
  });

  $('#dhl-label-package-type').change(function () {
    var id = $(this).find('option:selected').attr('value');
    $('.dhl-packages').hide(200);
    $('#dhl-package-' + id).show(400);
  });

  $(document).on('click', '.dhl-label-extracharge-insurance span', function () {
    toggleInsuredDeclaredValues();
  });
   $(document).on('click', '.dhl-label-extracharge-dangerous-goods .btn_DG span, .dhl-extracharge-non-doc .btn_DG span', function () {

       
        var id = $(this).find('input').attr('id'); 
        var i = 1;
        var k = 0;
        while(i < 12)
        {
            if ($('.btn_DG #extracharge-'+i+'_on').prop('checked')) {
                k = k + 1;
            }       
            i++;
        }

        if (k <= 3) {
            toggleExtrachargesDangerousGoods();
        } else {
            alert("You can't check more than three options ! ");
            disableExtrachargeDangerousGoods(id);
        }
         
  });
  $(document).on('click', '.dhl-label-doc span', function () {
    toggleExtracharges();
  });
  $(document).on('click', '.dhl_label_service', function () {
    toggleExtracharges();
  });
  $(document).on('click', '#submit-dhl-label-prices', function () {
    resetFormPlt();
  });
  $(document).on('click', '.dhl-label-extracharge-excepted span', function () {
    toggleExceptedQuantities();
  });
});

function disableExtrachargeDangerousGoods(id) {
    var i = 0;

    while (i < 12) {
        var extra = 'extracharge-' + i + '_on';
        if (id === extra) {
            $("#extracharge-" + i + "_on").prop("checked", false);
            $("#extracharge-" + i + "_off").prop("checked", true);
        }
        i++;
    }
}

function resetFormPlt()
{
    $('.msg_plt_not_eligible').hide();
    $('#panel_plt').hide();
    $('.panel_form_invoice').hide();
    $('#dhl-generate-label-and-invoice-block').hide(200);  
}

function displayPltOption(plt) {
    if (plt !== 0) {
        $('#panel_plt').show(400);
        $('#dhl-generate-label-block').hide(200);
        $('.msg_plt_not_eligible').hide(400);
//        delete invoices 
        $('.dhl-invoice-result-page-label').html('');
        document.getElementById("use_plt_option").checked = true; 
        usePltOption();       
    } else {
        $('#panel_plt').show(400);
        $('#dhl-generate-label-block').hide(200);
        $('.msg_plt_not_eligible').show(400);
        $('.dhl-invoice-result-page-label').html('');
        document.getElementById("use_plt_option").checked = true;
        usePltOption();
    }
}
function hiddePltOption(hideMsg = 0) {
    $('#panel_plt').hide(200);
    $('#dhl-generate-label-and-invoice-block').hide(200);
    $('#submit-dhl-label-create').show(400);
    $('#dhl-generate-label-block').show(400);
//    $('.panel_form_invoice').show(200).insertAfter($('.panel_plt'));
}

function usePltOption(){
    $('.js-message-data-edata').hide(200);
    if ($('.dhl-invoice-result-page-label').is(':empty')){
        $('.btn_display_form_invoice').show(200);
        $('.btn_view_invoice').hide(200);
        $('#dhl-generate-label-and-invoice-block').hide(200);
        setTimeout(function(){displayFormInvoice();}, 2000);
    } else {
        $('.btn_view_invoice').show(200);
        $('#dhl-generate-label-and-invoice-block').show(200);
    }
    $('#dhl-generate-label-block').show(200);
    $('.div_upload_pdf_invoice').hide(200);
}

function displayFormInvoice(){
    document.getElementById("use_plt_option").checked = true;    
    $('.panel_form_invoice').show(200);
    if ($('#extracharge-2_on').prop('checked')) {
        $('#dhl-invoice-incoterms option[value="DDP"]').prop('selected', true);
    }
    nbr_package_added = document.getElementById("dhl-package-table").rows.length - 1 ;
    $('#dhl-total-package').attr('value', nbr_package_added);
    $('#dhl-generate-label-block').show(200);
    $('#dhl-generate-label-and-invoice-block').hide(200);
    $('.btn_view_invoice').hide(200);
    if ($('.btn_display_form_invoice').is(":visible")) {
        $('.btn_display_form_invoice').hide(200);
    }
}

function displayFormInvoiceUpdate(){   
    $('.panel_form_invoice').show(200);
    if ($('#extracharge-2_on').prop('checked')) {
        $('#dhl-invoice-incoterms option[value="DDP"]').prop('selected', true);
    }
    $('.btn_view_invoice').hide(200);
    $('#dhl-generate-label-and-invoice-block').hide(400);
}

function showEdataMessage()
{
    $('.js-message-data-edata').show(400);
}

function uploadOwnInvoice(){
    $('.div_upload_pdf_invoice').show(400);
    $('.btn_display_form_invoice').hide();
    $('.js-message-data-edata').show(400);
    $('.panel_form_invoice').hide(200);
    $('#dhl-generate-label-block').hide(200);
    $('.btn_view_invoice').hide(200);
    if (document.getElementById("pdf_name_submitted").value === "") {
        $('#dhl-generate-label-and-invoice-block').hide(400);
    } else {
        $('.panel_form_invoice').show(200);
        if ($('#extracharge-2_on').prop('checked')) {
            $('#dhl-invoice-incoterms option[value="DDP"]').prop('selected', true);
        }
        $('#dhl-generate-label-and-invoice-block').show(200);
    }
}

function notUsePlt(){
   $('.btn_display_form_invoice').hide();
    $('#dhl-generate-label-block').show(400);
    $('#submit-dhl-label-create').show(400); 
    $('.div_upload_pdf_invoice').hide(200);
    $('.panel_form_invoice').hide(200);
    $('#dhl-generate-label-and-invoice-block').hide(200);
    $('.btn_view_invoice').hide(200);
}

function initInvoiceParams()
{
    document.getElementById("use_plt_option").checked = true; 
    $('.btn_display_form_invoice').show(400);
    $('.div_upload_pdf_invoice').hide(200);
    $('#dhl-generate-label-block').hide(200);
    $('#dhl-generate-label-and-invoice-block').hide(200);
    $('#submit-dhl-label-create').hide(200);
    
}

function DeleteInvoice(){
    $('#view_file_pdf').hide(200);
    $('.panel_form_invoice').hide(200);
    $('#form-upload_invoice').show(200);
    document.getElementById("eg_pdf_invoice-name").value = "";
    $('#dhl-generate-label-and-invoice-block').hide(400);
}

function desableExtrachargeDangerousGoods(id) {
    if (id === 'extracharge-3_on') {
        $("#extracharge-3_on").prop("checked", false);
        $("#extracharge-3_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_3').hide(400);
    }
    
    if (id === 'extracharge-4_on') {
        $("#extracharge-4_on").prop("checked", false);
        $("#extracharge-4_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_4').hide(400);
        $('#type_designation_div').hide(400);
    }
    
    if (id === 'extracharge-5_on') {
        $("#extracharge-5_on").prop("checked", false);
        $("#extracharge-5_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_5').hide(400);
    }
    
    if (id === 'extracharge-6_on') {
        $("#extracharge-6_on").prop("checked", false);
        $("#extracharge-6_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_6').hide(400);
    }
    
    if (id === 'extracharge-7_on') {
        $("#extracharge-7_on").prop("checked", false);
        $("#extracharge-7_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_7').hide(400);
    }
    
    if (id === 'extracharge-8_on') {
        $("#extracharge-8_on").prop("checked", false);
        $("#extracharge-8_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_8').hide(400);
    }
    
    if (id === 'extracharge-9_on') {
        $("#extracharge-9_on").prop("checked", false);
        $("#extracharge-9_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_9').hide(400);
    }
    
    if (id === 'extracharge-11_on') {
        $("#extracharge-11_on").prop("checked", false);
        $("#extracharge-11_off").prop("checked", true);
        $('.dhl_nbr_pieces_concerned_11').hide(400);
    }
}

function toggleExtrachargesDangerousGoods() {
    if ($('#extracharge-3_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_3').show(400);
    } else {
        $('.dhl_nbr_pieces_concerned_3').hide(400);
    }
    if ($('#extracharge-4_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_4').show(400);
    } else {
          $('.dhl_nbr_pieces_concerned_4').hide(400);
    }
    if ($('#extracharge-5_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_5').show(400);
    } else {
       $('.dhl_nbr_pieces_concerned_5').hide(400);
    }
    if ($('#extracharge-6_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_6').show(400);
    } else {
      $('.dhl_nbr_pieces_concerned_6').hide(400);  
    }
    if ($('#extracharge-7_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_7').show(400);
    } else {
      $('.dhl_nbr_pieces_concerned_7').hide(400);  
    }
    if ($('#extracharge-8_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_8').show(400);
    } else {
        $('.dhl_nbr_pieces_concerned_8').hide(400);
    }
    if ($('#extracharge-9_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_9').show(400);
    } else {
        $('.dhl_nbr_pieces_concerned_9').hide(400);      
    }
    if ($('#extracharge-11_on').prop('checked')) {
      $('.dhl_nbr_pieces_concerned_11').show(400);
    } 
    else {
      $('.dhl_nbr_pieces_concerned_11').hide(200);
    }
}
function updateTotalPicesDangerousGoods() {
    if (!$('.dhl-services-result').is(':empty')) {
        alert("You must reload the service DHL after add or delete package.");
        $('#panel_plt').hide(200);
        $('.dhl-services-result').html('');
        $('.dhl-label-result').html('');
        $('#dhl-generate-label-block').hide();
        $('#dhl-generate-label-and-invoice-block').hide(200);
    }
    resetValueDangerousGouds();
    nbr_package_added = document.getElementById("dhl-package-table").rows.length - 1 ;
    $(".dhl-total-pieces-concerned").text(" / "+nbr_package_added);
    $('#dhl-total-pieces-concerned2').attr('value', nbr_package_added);
    $('#dhl-total-package').attr('value', nbr_package_added);
}


function declaredValueControl() {
  var num = document.getElementById("dhl-label-declared-value").value;
  if (num.indexOf(".")!== -1){
    var n = parseFloat(num).toFixed(2);
    $('#dhl-label-declared-value').attr('value', n);
  }
}

function toggleExceptedQuantities(){
  if ($('#extracharge-4_on').prop('checked')) {
    $('#type_designation_div').show(400);
  } else {
    $('#type_designation_div').hide(200);
  }
}

function resetValueDangerousGouds(){
    $('.dhl-number-pieces-concerned').attr('value', '');
}