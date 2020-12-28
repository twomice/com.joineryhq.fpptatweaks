CRM.$(function($) {
  $(document).ready(function(){
    var $contactId = $('#org_id').parents('tr');
    var $relationshipTypeId = $('#org_relationship').parents('tr');
    $contactId.css('display','block').addClass('crm-section editrow_org_id-section form-item').attr('id','editrow-org_id').append('<div class="clear"></div>').find('td:nth-child(2)').addClass('edit-value content');
    $relationshipTypeId.css('display','block').addClass('crm-section editrow_org_relationship-section form-item').attr('id','editrow-org_relationship').append('<div class="clear"></div>').find('td:nth-child(2)').addClass('edit-value content');
    $contactId.insertBefore('#editrow-first_name');
    $relationshipTypeId.insertBefore('#editrow-first_name');
    $('.form-layout-compressed').remove();
  });
});
