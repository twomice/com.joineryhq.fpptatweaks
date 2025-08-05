<?php

/**
 * API Wrapper for invoicespecialvalues.get
 *
 */
class CRM_Fpptatweaks_APIWrappers_Invoicespecialvalues_Get {

  /**
   * No changes to the API request.
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Augment the result with additional values.
   */
  public function toApiOutput($apiRequest, $result) {
    // Invoicespecialvalues.get requires 'id' parameter and only returns for
    // one contribution, so get that id from request params.
    // But don't bother with this if we don't have fpptaqb extension and its
    // handy utility for getting the name of the attributed organization
    // on any given contribution.
    if (is_callable('CRM_Fpptaqb_Utils_Invoice::getAttributedContactId')) {
      // Get the provided contribution id.
      $id = $apiRequest['params']['id'];
      // Use fpptaqb to get the attributed organization id for that contribution.
      $attributedOrgId = CRM_Fpptaqb_Utils_Invoice::getAttributedContactId($id);
      if ($attributedOrgId) {
        // get the display name of that org contact.
        $attributedOrgDisplayName = civicrm_api3('Contact', 'getvalue', [
          'return' => "display_name",
          'id' => $attributedOrgId,
        ]);
        $result['values'][0]['attributedOrgDisplayName'] = $attributedOrgDisplayName;
      }
    }

    return $result;
  }

}
