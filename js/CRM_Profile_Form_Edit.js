CRM.$(function($) {
  $(document).ready(function(){
    var $contactId = $('#contact_id').parents('tr');
    var $relationshipTypeId = $('#relationship_type_id').parents('tr');
    $contactId.css('display','block').addClass('crm-section editrow_contact_id-section form-item').attr('id','editrow-contact_id').append('<div class="clear"></div>').find('td:nth-child(2)').addClass('edit-value content');
    $relationshipTypeId.css('display','block').addClass('crm-section editrow_relationship_type_id-section form-item').attr('id','editrow-relationship_type_id').append('<div class="clear"></div>').find('td:nth-child(2)').addClass('edit-value content');
    $contactId.insertBefore('#editrow-first_name');
    $relationshipTypeId.insertBefore('#editrow-first_name');
    $('.form-layout-compressed').remove();
  });
});
