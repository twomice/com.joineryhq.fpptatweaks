<?php

require_once 'fpptatweaks.civix.php';
// phpcs:disable
use CRM_Fpptatweaks_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_alterTemplateFile().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterTemplateFile
 */
function fpptatweaks_civicrm_alterTemplateFile($formName, &$form, $context, &$tplName) {
  /*
   * We'll test if this is a profile page, and if that profile is the one specified
   * as the 'cppt history profile' in settings. If so, we'll replace the template
   * with our own, and we'll fetch some data to populate that template.
   */
  if ($formName == 'CRM_Profile_Page_Dynamic' && $gid = $form->getVar('_gid')) {
    $cpptHistoryGid = Civi::settings()->get('fpptatweaks_cppt_history_profile');
    if ($gid == $cpptHistoryGid) {
      // Specify we're using a different template.
      $tplName = "CRM/Fpptatweaks/hooked/Page/cpptHistory.tpl";

      // Get id of dashboard contact
      $cid = $form->getVar('_id');
      if (empty($cid)) {
        // If there's no cid, we can't do anything; we shouldn't be here, really. Just return.
        return;
      }

      // Get CPPT History data for this contact.
      // Api4 makes multi-value custom values easy.
      $cpptHistories = \Civi\Api4\CustomValue::get('CPPT_History')
        ->setCheckPermissions(FALSE)
        ->addWhere('entity_id', '=', $cid)
        ->addOrderBy('Event_Date', 'DESC')
        ->setLimit(0)
        ->execute();
      // Build rows for template  output.
      $rows = [];
      foreach ($cpptHistories as $cpptHistory) {
        $rows[] = $cpptHistory;
      }
      $template = CRM_Core_Smarty::singleton();
      $template->assign('rows', $rows);
      // Get the dateformatFull string and pass it into the template for use in crmDate
      $config = CRM_Core_Config::singleton();
      $template->assign('dateFormat', $config->dateformatFull);
    }
  }
}

/**
 * Implements hook_civicrm_alterContent().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterContent
 */
function fpptatweaks_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  if ($context == 'page' && ($object->getVar('_name') == 'CRM_Contact_Page_View_UserDashBoard')) {
    // NOTE: Javascript relevant to this page is added in fpptatweaks_civicrm_pageRun().
    $userCid = CRM_Core_Session::singleton()->getLoggedInContactID();
    $relatedOrgs = CRM_Fpptatweaks_Util::getPermissionedContacts($userCid, NULL, NULL, 'Organization');

    if (!$object->_contactId || !array_key_exists($object->_contactId, $relatedOrgs)) {
      // If user has no permissioned relationships to organizations, they can't use the button, so return.
      return;
    }
    $ufgroupId = Civi::settings()->get('fpptatweaks_new_relationship_profile');
    if (!$ufgroupId) {
      // If there's no known ufgroup id, we can't display the button, so return.
      return;
    }

    // Process the snippet template and inject the parsed content.
    $tpl = CRM_Core_Smarty::singleton();
    $tpl->assign('ufgroupId', $ufgroupId);
    $tpl->assign('orgId', $object->_contactId);
    $fpptatweaksContent = $tpl->fetch('CRM/Fpptatweaks/snippet/injectedButton.tpl');
    $content .= $fpptatweaksContent;

  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function fpptatweaks_civicrm_config(&$config) {
  _fpptatweaks_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function fpptatweaks_civicrm_xmlMenu(&$files) {
  _fpptatweaks_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function fpptatweaks_civicrm_install() {
  _fpptatweaks_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function fpptatweaks_civicrm_postInstall() {
  _fpptatweaks_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function fpptatweaks_civicrm_uninstall() {
  _fpptatweaks_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function fpptatweaks_civicrm_enable() {
  _fpptatweaks_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function fpptatweaks_civicrm_disable() {
  _fpptatweaks_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function fpptatweaks_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _fpptatweaks_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function fpptatweaks_civicrm_managed(&$entities) {
  _fpptatweaks_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function fpptatweaks_civicrm_caseTypes(&$caseTypes) {
  _fpptatweaks_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function fpptatweaks_civicrm_angularModules(&$angularModules) {
  _fpptatweaks_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function fpptatweaks_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _fpptatweaks_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function fpptatweaks_civicrm_entityTypes(&$entityTypes) {
  _fpptatweaks_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function fpptatweaks_civicrm_themes(&$themes) {
  _fpptatweaks_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function fpptatweaks_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function fpptatweaks_civicrm_navigationMenu(&$menu) {
//  _fpptatweaks_civix_insert_navigation_menu($menu, 'Mailings', array(
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civhttps://docs.civicrm.org/dev/en/stable/hooks/hook_civicrm_permission/icrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ));
//  _fpptatweaks_civix_navigationMenu($menu);
//}

/**
 * Implements hook_civicrm_pageRun().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_pageRun
 */
function fpptatweaks_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');

  if ($pageName == 'CRM_Contact_Page_View_UserDashBoard') {
    // Must add script file here because it can't be  done from fpptatweaks_civicrm_alterContent().
    CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.fpptatweaks', 'js/fpptatweaks.js', 100, 'page-footer');
  }
}

/**
 * Log CiviCRM API errors to CiviCRM log.
 */
function _fpptatweaks_log_api_error(CiviCRM_API3_Exception $e, string $entity, string $action, array $params) {
  $message = "CiviCRM API Error '{$entity}.{$action}': " . $e->getMessage() . '; ';
  $message .= "API parameters when this error happened: " . json_encode($params) . '; ';
  $bt = debug_backtrace();
  $error_location = "{$bt[1]['file']}::{$bt[1]['line']}";
  $message .= "Error API called from: $error_location";
  CRM_Core_Error::debug_log_message($message);
}

/**
 * CiviCRM API wrapper. Wraps with try/catch, redirects errors to log, saves
 * typing.
 */
function _fpptatweaks_civicrmapi(string $entity, string $action, array $params, bool $silence_errors = TRUE) {
  try {
    $result = civicrm_api3($entity, $action, $params);
  }
  catch (CiviCRM_API3_Exception $e) {
    _fpptatweaks_log_api_error($e, $entity, $action, $params);
    if (!$silence_errors) {
      throw $e;
    }
  }

  return $result;
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function fpptatweaks_civicrm_navigationMenu(&$menu) {
  $pages = array(
    'settings_page' => array(
      'label'      => E::ts('FPPTA Tweaks Settings'),
      'name'       => 'Settings',
      'url'        => 'civicrm/admin/fpptatweaks/settings?reset=1',
      'parent'    => array('Administer', 'Customize Data and Screens'),
      'permission' => 'administer CiviCRM',
    ),
  );

  foreach ($pages as $item) {
    // Check that our item doesn't already exist.
    $menu_item_search = array('url' => $item['url']);
    $menu_items = array();
    CRM_Core_BAO_Navigation::retrieve($menu_item_search, $menu_items);
    if (empty($menu_items)) {
      $path = implode('/', $item['parent']);
      unset($item['parent']);
      _fpptatweaks_civix_insert_navigation_menu($menu, $path, $item);
    }
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * Set a default value for an event price set field.
 *
 */
function fpptatweaks_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Profile_Form_Edit') {
    // get the fpptatweaks_new_relationship_profile value
    $ufgroupId = Civi::settings()->get('fpptatweaks_new_relationship_profile');
    // proceed if match on the gorup id
    if ($form->getVar('_gid') == $ufgroupId) {
      // Get logged in user id
      $userCid = CRM_Core_Session::singleton()->getLoggedInContactID();
      if (CRM_Fpptatweaks_Util::hasPermissionedRelatedContact($userCid, 'Organization')) {
        $relatedOrgs = CRM_Fpptatweaks_Util::getPermissionedContacts($userCid, NULL, NULL, 'Organization');
        $organizationOptions = [];
        foreach ($relatedOrgs as $relatedOrgCid => $relatedOrg) {
          $organizationOptions[$relatedOrgCid] = $relatedOrg['name'];
        }
        $form->add('select', 'org_id', E::ts('Organization'), $organizationOptions, TRUE, [
          'class' => 'crm-select2',
          'style' => 'width: 100%;',
          'placeholder' => '- ' . E::ts('Select') . ' -',
        ]);

        $relationshipTypeOptions = CRM_Fpptatweaks_Util::getRelationshipTypeOptions('Organization');
        $form->add('select', 'org_relationship', E::ts("Relationship"), $relationshipTypeOptions, TRUE, [
          'class' => 'crm-select2',
          'style' => 'width: 100%;',
          'placeholder' => '- ' . E::ts('Select') . '-',
        ]);

        $tpl = CRM_Core_Smarty::singleton();
        $bhfe = $tpl->get_template_vars('beginHookFormElements');
        if (!$bhfe) {
          $bhfe = array();
        }
        $bhfe[] = 'org_id';
        $bhfe[] = 'org_relationship';
        $form->assign('beginHookFormElements', $bhfe);

        CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.fpptatweaks', 'js/CRM_Profile_Form_Edit.js');
        CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.fpptatweaks', 'css/CRM_Profile_Form_Edit.css', 100, 'page-header');

        $form->setDefaults(['org_id' => CRM_Utils_Request::retrieve('orgId', 'Integer')]);
      }
      else {
        CRM_Core_Error::statusBounce(E::ts('You do not have any organizations for which you can submit new relationships.'));
      }
    }
  }
  elseif ($formName == 'CRM_Contribute_Form_Search') {
    // Remove default values for 'status' field in search criteria.
    $form->setDefaults(['contribution_status_id' => 0]);
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 */
function fpptatweaks_civicrm_postProcess($formName, $form) {
  if ($formName == 'CRM_Profile_Form_Edit') {
    // get the fpptatweaks_new_relationship_profile value
    $ufgroupId = Civi::settings()->get('fpptatweaks_new_relationship_profile');
    // proceed if match on the group id
    if ($form->getVar('_gid') == $ufgroupId) {
      // The individual contact ID, whether newly created or dedupe-matched to an existing,
      // is in $form->_id.
      $individualId = $form->getVar('_id');

      $userDisplayName = CRM_Core_Session::singleton()->getLoggedInContactDisplayName();
      $organizationId = $form->_submitValues['org_id'];
      list($relationshipTypeId, $rpos1, $rpos2) = explode('_', $form->_submitValues['org_relationship']);
      $permissionColumn = "is_permission_{$rpos1}_{$rpos2}";

      $relationship = \Civi\Api4\Relationship::create()
        ->setCheckPermissions(FALSE)
        ->addValue('contact_id_' . $rpos1, $organizationId)
        ->addValue('contact_id_' . $rpos2, $individualId)
        ->addValue('description', E::ts("Relationship created by request of {$userDisplayName}"))
        ->addValue($permissionColumn, 1)
        ->addValue('relationship_type_id', $relationshipTypeId)
        ->addValue('is_active', 0);

      if (Civi::settings()->get('fpptatweaks_new_relationship_tag')) {
        // This would also mean we're configured to tag such additional
        // participant contacts for review; do so now.
        if ($tagId = Civi::settings()->get('fpptatweaks_new_relationship_tag')) {
          $entityTag = \Civi\Api4\EntityTag::create()
            ->setCheckPermissions(FALSE)
            ->addValue('tag_id', $tagId)
            ->addValue('entity_table', 'civicrm_contact')
            ->addValue('entity_id', $individualId)
            // We need to tag this contact, regardless of our write access to the contact; thus, skip perm checks.
            ->execute();
        }
      }

      try {
        $relationship
          // We need to save this relationship, regardless of our write access to the contact; thus, skip perm checks.
          ->setCheckPermissions(FALSE)
          ->execute();
      }
      catch (Exception $e) {
        // If the error is because relationship already exists, we can ignore
        // it. Otherwise, throw it to be handled upstream.
        if ($e->getMessage() != 'Duplicate Relationship') {
          throw $e;
        }
      }

      // Create an activity recording this "new relationship" request.
      $activityTypeOptionValue = \Civi\Api4\OptionValue::get()
        ->setCheckPermissions(FALSE)
        ->addWhere('option_group_id', '=', 2)
        ->addWhere('name', '=', 'Request relationship start')
        ->execute()
        ->first();
      $contactIndividual = \Civi\Api4\Contact::get()
        ->setCheckPermissions(FALSE)
        ->addSelect('display_name')
        ->addWhere('id', '=', $individualId)
        ->execute()
        ->first();
      $contactOrganization = \Civi\Api4\Contact::get()
        ->setCheckPermissions(FALSE)
        ->addSelect('display_name')
        ->addWhere('id', '=', $organizationId)
        ->execute()
        ->first();
      $relationshipType = \Civi\Api4\RelationshipType::get()
        ->setCheckPermissions(FALSE)
        ->addSelect('label_b_a')
        ->addWhere('id', '=', $relationshipTypeId)
        ->execute()
        ->first();
      $subject = E::ts('User requests: add %1 (%2 / %3)', [
        '1' => $relationshipType['label_b_a'],
        '2' => $contactIndividual['display_name'],
        '3' => $contactOrganization['display_name'],
      ]);
      $relationshipCreate = \Civi\Api4\Activity::create()
        ->setCheckPermissions(FALSE)
        ->addValue('activity_type_id', $activityTypeOptionValue['value'])
        ->addValue('Start_relationship_details.RshipStart_Contact_Org', $organizationId)
        ->addValue('Start_relationship_details.RshipStart_Contact_Indiv', $individualId)
        ->addValue('subject', $subject)
        ->addValue('Start_relationship_details.RshipStart_Relationship_type_name', $relationshipType['label_b_a'])
        ->addValue('source_contact_id', CRM_Core_Session::getLoggedInContactID())
        ->addChain('name_me_0', \Civi\Api4\ActivityContact::create()
          ->setCheckPermissions(FALSE)
          ->addValue('activity_id', '$id')
          ->addValue('record_type_id:name', 'Activity Targets')
          ->addValue('contact_id', $organizationId)
        )
        ->addChain('name_me_1', \Civi\Api4\ActivityContact::create()
          ->setCheckPermissions(FALSE)
          ->addValue('activity_id', '$id')
          ->addValue('record_type_id:name', 'Activity Targets')
          ->addValue('contact_id', $individualId)
        )
        ->execute();
      CRM_Core_Session::setStatus(E::ts('Your request is pending review by FPPTA staff.'), '', 'success');
    }
  }
}

/**
 * Implements hook_civicrm_searchColumns().
 *
 */
function fpptatweaks_civicrm_searchColumns($objectName, &$headers, &$rows, &$selector) {
  // Check if it is search contribution
  // !empty($rows) will prevent sql error if contact doesn't have contribution
  if (
    ($objectName == 'contribution'
    || $objectName == 'event')
    && !empty($rows)
  ) {
    // Since we don't want it to apply in event and contribution search, we will just return
    if (CRM_Utils_System::getUrlPath() == 'civicrm/event/search'
      || CRM_Utils_System::getUrlPath() == 'civicrm/contribute/search'
    ) {
      return;
    }

    // Insert additional column header in the tab
    $insertedHeader = [
      'name' => E::ts('Inv #'),
      'field_name' => 'invoice_number',
      'direction' => CRM_Utils_Sort::DONTCARE,
      'weight' => 45,
    ];

    if ($objectName == 'event') {
      // Column position of the inserted header, this is also useful
      // ..when injecting the js for the value
      $insertHeaderPosition = 5;
      array_splice($headers, $insertHeaderPosition, 0, [$insertedHeader]);
      // Add weight on the header for the final position of the columns
      foreach ($headers as $headerKey => $header) {
        $headers[$headerKey]['weight'] = $headerKey;
      }

      // Get participantIds array of column data
      $participantIdsPerRow = CRM_Utils_Array::collect('participant_id', $rows);
      $contributionIdsPerRow = [];
      // Loop participantIds array to get contribution ID
      foreach ($participantIdsPerRow as $rowKey => $participantId) {
        $paymentDetails = civicrm_api3('ParticipantPayment', 'get', [
          'sequential' => 1,
          'participant_id' => $participantId,
        ]);

        // Save contributionIds as array
        $contributionIdsPerRow[$rowKey] = $paymentDetails['values'][0]['contribution_id'];
      }

      // Flip for the rows position
      $rowKeyPerContributionId = array_flip($contributionIdsPerRow);
    }
    elseif ($objectName == 'contribution') {
      $headers[] = $insertedHeader;
      $contributionIdsPerRow = CRM_Utils_Array::collect('contribution_id', $rows);
      $rowKeyPerContributionId = array_flip($contributionIdsPerRow);
    }

    $contributions = \Civi\Api4\Contribution::get()
      ->setCheckPermissions(FALSE)
      ->addSelect('id', 'invoice_number')
      ->addWhere('id', 'IN', $contributionIdsPerRow)
      ->execute();

    foreach ($contributions as $contribution) {
      $rows[$rowKeyPerContributionId[$contribution['id']]]['invoice_number'] = $contribution['invoice_number'];
    }

    if ($objectName == 'event') {
      // Inject data in js
      $fpptatweaks['eventRows'] = $rows;
      $fpptatweaks['columnPosition'][] = $insertHeaderPosition;
      CRM_Core_Resources::singleton()->addVars('fpptatweaks', $fpptatweaks);
      CRM_Core_Resources::singleton()->addScriptFile('com.joineryhq.fpptatweaks', 'js/searchColumns_event.js', 100, 'page-footer');
    }
  }
}
