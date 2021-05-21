CRM.$(function($) {
  // Foreach data that has inventoryfield
  $.each(CRM.vars.fpptatweaks.eventRows, function(rowKey, row) {
    var invoiceNumber = row.invoice_number ? row.invoice_number : '';
    $('.CRM_Event_Form_Search table > tbody > tr.crm-event:nth-child(' + (rowKey + 1) + ') td:nth-child(' + CRM.vars.fpptatweaks.columnPosition[0] + ')').after('<td class="crm-participant-invoice_number">' + invoiceNumber + '</td>');
  });
  $('.form-item > table > tbody > tr:last-child').append('<td></td>');
});
