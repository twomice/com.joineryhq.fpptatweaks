/*global CRM, ts */
CRM.$(function($) {
  $( document ).ajaxStop(function() {
    // Tweak contents of the "Your Event(s)" secion.
    $('.crm-dashboard-civievent .view-content tbody > tr').each(function(){
      // Remove link to relationship (in "Relationship" column")
      var eventTitle = $('> td:first-child a', this).text();
      if($('> td:first-child a', this).length) {
        $('> td:first-child', this).append('<span>' + eventTitle + '</span>').find('a:first-child').remove();
      }
    });
    // Also remove the description in this section, which tends to imply that the user
    // can "Click on the event name for more information."
    $('.crm-dashboard-civievent .view-content .description').hide();

    // Tweak contents of the "Your Contacts / Organizations" secion.
    var requestEndRelationshipUrl, dasbhoardLink;
    // Store the current page url as a base64 string (because we're going to append
    // this value as a query string parameter, and we want to avoid potential url
    // syntax/encoding problems.
    var dashbase = btoa(window.location.href);
    $('.crm-contact-relationship-user tbody > tr').each(function(){
      // Remove link to relationship (in "Relationship" column"), and
      // also remove link to related contact (in second column).
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

      // Append 'dashbase' param to Dashboard links (for use by CRM_Fpptatweaks_Page_MyDashboard)
      // (The [x-fpptatweaks-is-dashboard-link] attribute is added in fpptatweaks_civicrm_links().)
      dashboardLink = $('[x-fpptatweaks-is-dashboard-link]', this)[0];
      if (dashboardLink) {
        dashboardLink.href += '&dashbase=' + dashbase;
      }
    });
  });

  // Tweak contents of "Your Contribution(s)":
  // Replace 'Finicancial Type' table header with 'Source'
  $('.crm-dashboard-civicontribute .view-content table tr th:nth-child(2)').html(ts('Source'));

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
