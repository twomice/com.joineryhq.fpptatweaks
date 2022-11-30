CRM.$(function($) {
  CRM.$( document ).ajaxStop(function() {
    var requestEndRelationshipUrl;
    // Tweak contents of the "Your Contacts / Organizations" secion.
    CRM.$('.crm-contact-relationship-user tbody > tr').each(function(){
      // Remove link to relationship (in "Relationship" column")
      var relText = $('> td:first-child a', this).text(),
          relOrgIcon = $('> td:nth-child(2) a:first-child', this).html(),
          relOrgText = $('> td:nth-child(2) a:nth-child(2)', this).text();
      if($('> td:first-child a', this).length) {
        $('> td:first-child', this).append('<span>' + relText + '</span>').find('a:first-child').remove();
        $('> td:nth-child(2)', this).append(relOrgIcon + '<span>' + relOrgText + '</span>').find('a').remove();
      }

      // Insert 'remove relationship' link
      requestEndRelationshipUrl = CRM.url('civicrm/fppta/reqendrship', {rid: $(this).attr('data-id')});
      $('> td:last-child a[title="Disable Relationship"]', this).after('<a href="' + requestEndRelationshipUrl + '">Remove</a>');

      // Remove 'disable relationship' link
      $('> td:last-child a[title="Disable Relationship"]', this).remove();
    });
  });

  // Check if there is an injected element
  if ($('.fpptatweaks-inject').length) {
    // Get the injected element
    var injectedElements = $('.fpptatweaks-inject').html();
    // Place injected element in Your Contacts / Organizations row
    $('.crm-dashboard-permissionedOrgs td').append(injectedElements);
    // Remove injected element
    $('.fpptatweaks-inject').remove();
  }
});
