<?php

// phpcs:disable
use CRM_Fpptatweaks_ExtensionUtil as E;
// phpcs:enable

class CRM_Fpptatweaks_Util {

  public static function getRelationshipTypeOptions($otherContactType) {
    $relationshipTypeOptions = [];
    $relationshipTypes = \Civi\Api4\RelationshipType::get()
      ->setCheckPermissions(FALSE)
      ->addWhere('is_active', '=', 1)
      ->addClause('OR',
        [
          'AND', [
            ['contact_type_a', '=', 'Individual'],
            ['contact_type_b', '=', $otherContactType],
          ],
        ],
        [
          'AND', [
            ['contact_type_a', '=', $otherContactType],
            ['contact_type_b', '=', 'Individual'],
          ],
        ]
      )
      ->execute();
    foreach ($relationshipTypes as $relationshipType) {
      if ($relationshipType['contact_type_a'] == $otherContactType) {
        $relationshipTypeOptions["{$relationshipType['id']}_a_b"] = $relationshipType['label_a_b'];
      }
      if ($relationshipType['contact_type_b'] == $otherContactType) {
        $relationshipTypeOptions["{$relationshipType['id']}_b_a"] = $relationshipType['label_b_a'];
      }
    }
    // TODO: support limitation of these types (and possibly re-labeling of them)
    // in the UI.
    return $relationshipTypeOptions;
  }

  public static function hasPermissionedRelatedContact($cid, $contactType = NULL) {
    if (!isset(Civi::$statics[__CLASS__][__FUNCTION__])) {
      Civi::$statics[__CLASS__][__FUNCTION__] = [];
    }
    if (!isset(Civi::$statics[__CLASS__][__FUNCTION__][$cid][$contactType])) {
      Civi::$statics[__CLASS__][__FUNCTION__][$cid][$contactType] = FALSE;
      $relationships = CRM_Contact_BAO_Relationship::getRelationship($cid, CRM_Contact_BAO_Relationship::CURRENT, NULL, NULL, NULL, NULL, NULL, TRUE);
      if ($contactType) {
        foreach ($relationships as $relationshipId => $relationship) {
          if (
            $relationship['contact_type'] == $contactType
            && ($relationship["is_permission_{$relationship['rtype']}"] == 1)
          ) {
            Civi::$statics[__CLASS__][__FUNCTION__][$cid][$contactType] = TRUE;
            break;
          }
        }
      }
      else {
        Civi::$statics[__CLASS__][__FUNCTION__][$cid][$contactType] = !empty($relationships);
      }
    }
    return Civi::$statics[__CLASS__][__FUNCTION__][$cid][$contactType];
  }

  /**
   * Function to return list of permissioned contacts for a given contact and relationship type.
   * Copied and modified from CRM_Contact_BAO_Relationship::getPermissionedContacts(), with
   * improvements made to support both a=>b and b=>a relationship types.
   *
   * @param int $contactID
   *   contact id whose permissioned contacts are to be found.
   * @param int $relTypeId
   *   one or more relationship type id's.
   * @param string $name
   * @param string $contactType
   *
   * @return array
   *   Array of contacts
   */
  public static function getPermissionedContacts($contactID, $relTypeId = NULL, $name = NULL, $contactType = NULL) {
    $contacts = [];
    $args = [1 => [$contactID, 'Integer']];
    $relationshipTypeClause = $contactTypeClause = '';

    if ($relTypeId) {
      // @todo relTypeId is only ever passed in as an int. Change this to reflect that -
      // probably being overly conservative by not doing so but working on stable release.
      $relationshipTypeClause = 'AND cr.relationship_type_id IN (%2) ';
      $args[2] = [$relTypeId, 'String'];
    }

    if ($contactType) {
      $contactTypeClause = ' AND cr.relationship_type_id = crt.id AND  if(cr.contact_id_a = %1, crt.contact_type_b, crt.contact_type_a) = %3 ';
      $args[3] = [$contactType, 'String'];
    }

    $query = "
SELECT cc.id as id, cc.sort_name as name
FROM civicrm_relationship cr, civicrm_contact cc, civicrm_relationship_type crt
WHERE
  (
    (
      cr.contact_id_a         = %1 AND
      cr.is_permission_a_b    = 1
    )
  OR
    (
      cr.contact_id_b         = %1 AND
      cr.is_permission_b_a    = 1
    )
  ) AND
  cc.id = if(cr.contact_id_a = %1, cr.contact_id_b, cr.contact_id_a) AND
  IF(cr.end_date IS NULL, 1, (DATEDIFF( CURDATE( ), cr.end_date ) <= 0)) AND
  cr.is_active = 1 AND
  cc.is_deleted = 0
  $relationshipTypeClause
  $contactTypeClause
";

    if (!empty($name)) {
      $name = CRM_Utils_Type::escape($name, 'String');
      $query .= "
AND cc.sort_name LIKE '%$name%'";
    }

    $dao = CRM_Core_DAO::executeQuery($query, $args);
    while ($dao->fetch()) {
      $contacts[$dao->id] = [
        'name' => $dao->name,
        'value' => $dao->id,
      ];
    }

    return $contacts;
  }

  /**
   * Define the sections of User Dashboard (civicrm/user) for which this extension
   * will support appending arbitrary html. Created for https://joinery.freshdesk.com/helpdesk/tickets/1645
   * which requested the ability to append a link below the Membership tab,
   * so this method was first written to support only that section. But in theory,
   * any section could be so supported.
   *
   * @return array
   *   Array of key/value pairs, in which:
   *     key is the section name as shown in the "crm-dashboard-*" classname for the
   *       dashboard section <tr> element. (e.g. for members section, this tr is
   *       class="crm-dashboard-civimember";
   *     value is the human-readable name to be used in labels for the relevant
   *       field in the extension settings page.
   */
  public static function getSupportedDashboardSectionAppends() {
    return array(
      'civimember' => E::ts('Memberships'),
    );
  }

  /**
   * STRICTLY for settings managed by this extension which are editable only by
   * logged-in admin users.
   *
   * @param string $htmlString
   * @return string
   */
  public static function sanitizeAdminSettingHtml($htmlString) {
    // Why use decodeValue() here? It goes like this:
    // - This value is from a setting managed by this extension (defined in fpptatweaks.setting.php).
    // - This extension saves settings through civicrm_api3('setting', 'create'),
    //   which causes the values to be passed through encodeValue(), essentially
    //   passing them through htmlspecialchars(). That is a security measure deep
    //   in the bones of CiviCRM.
    // - We trust this field value (just as CiviCRM trusts fields named 'help_pre',
    //   'description', etc., per CRM_Utils_API_HTMLInputCoder::getSkipFields()),
    //   because it can only be set by logged-in admin users.
    // - Since we trust it, we _wish_ there were a way to add it to CiviCRM's
    //   "skipped fields" (fields deemed save and not automatically encoded),
    //   but there's no way to do that.
    // - THEREFORE: We admit defeat on the input/encoding front, and here simply
    //   reverse that encodeing on output.
    $ret = CRM_Utils_API_HTMLInputCoder::singleton()->decodeValue($htmlString);
    // Despite assurances of "safe values" mentioned above, we'll still run
    // this content through HTML Purifier, just in case.
    $ret = CRM_Utils_String::purifyHTML($ret);
    return $ret;
  }

}
