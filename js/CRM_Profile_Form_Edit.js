CRM.$(function($) {
  $(document).ready(function(){
    // Identify bhfe table.
    var bhfeTable = $('#org_id').closest('table');
    bhfeTable.addClass('fpptatweaks-bhfe');
    // Move bhfe table before first form item.
    $('div#crm-profile-block  div.crm-section.form-item').first().before(bhfeTable);
  });
});
