CRM.$(function($) {
  CRM.$( document ).ajaxStop(function() {
    CRM.$('.crm-contact--selector-user tbody > tr').each(function(){
      var relText = $('> td:first-child a', this).text(),
          relOrgIcon = $('> td:nth-child(2) a:first-child', this).html(),
          relOrgText = $('> td:nth-child(2) a:nth-child(2)', this).text();

      $('> td:first-child', this).append('<span>' + relText + '</span>').find('a:first-child').remove();
      $('> td:nth-child(2)', this).append(relOrgIcon + '<span>' + relOrgText + '</span>').find('a').remove();
    });
  });
});
