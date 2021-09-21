<?php
use CRM_Fpptatweaks_ExtensionUtil as E;

class CRM_Fpptatweaks_Page_Event_ParticipantListing_NameAndOrganization extends CRM_Core_Page {

  public function preProcess() {
    $this->_id = CRM_Utils_Request::retrieve('id', 'Integer', $this, TRUE);

    // ensure that there is a particpant type for this
    $this->_participantListingID = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event',
      $this->_id,
      'participant_listing_id'
    );
    if (!$this->_participantListingID) {
      CRM_Core_Error::statusBounce(ts("The Participant Listing feature is not currently enabled for this event."));
    }

    // retrieve Event Title and include it in page title
    $this->_eventTitle = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event',
      $this->_id,
      'title'
    );
    $pageTitle = ts('%1 - Participants', [1 => $this->_eventTitle]);
    CRM_Utils_System::setTitle($pageTitle);
    $this->assign('fpptaTweaksPageTitle', $pageTitle);

    // we do not want to display recently viewed contacts since this is potentially a public page
    $this->assign('displayRecent', FALSE);
  }

  /**
   * @return string
   */
  public function run() {
    $this->preProcess();

    $fromClause = "
      FROM
        civicrm_contact c
        INNER JOIN civicrm_participant p ON p.contact_id = c.id
        INNER JOIN civicrm_option_value ov ON ov.value = p.role_id
            AND ov.filter = 1
        INNER JOIN civicrm_option_group og on og.id = ov.option_group_id
          AND og.name = 'participant_role'
        LEFT JOIN civicrm_contact ec
          ON ec.id = c.employer_id
          AND ec.is_deleted = 0
    ";

    $whereClause = "
      WHERE p.event_id = %1
      AND   p.is_test = 0
      AND   p.status_id IN ( 1, 2, 5 )
      AND   c.is_deleted = 0
    ";

    $params = [1 => [$this->_id, 'Integer']];
    $orderBy = $this->orderBy();

    $query = "
      SELECT distinct
        c.display_name,
        ec.display_name AS organization
               $fromClause
               $whereClause
      ORDER BY $orderBy
    ";

    $rows = [];
    $object = CRM_Core_DAO::executeQuery($query, $params);
    $statusLookup = CRM_Event_PseudoConstant::participantStatus(NULL, NULL, 'label');
    while ($object->fetch()) {
      $rows[] = $object->toArray();
    }
    $this->assign_by_ref('rows', $rows);

    CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.fpptatweaks', 'css/CRM_Fpptatweaks_Page_Event_ParticipantListing_NameAndOrganization.css');
    if (empty($this->_print)) {
      CRM_Core_Resources::singleton()->addStyleFile('com.joineryhq.fpptatweaks', 'css/CRM_Fpptatweaks_Page_Event_ParticipantListing_NameAndOrganization_print.css');
      $printLinkUrl = CRM_Utils_System::url('civicrm/event/participant', "reset=1&id={$this->_id}&snippet=3");
      $this->assign('printLinkUrl', $printLinkUrl);
    }

    return parent::run();
  }

  /**
   * @return string
   */
  public function orderBy() {
    static $headers = NULL;
    if (!$headers) {
      $headers = [];
      $headers[1] = [
        'name' => ts('Name'),
        'sort' => 'c.sort_name',
        'direction' => CRM_Utils_Sort::ASCENDING,
      ];
      $headers[2] = [
        'name' => ts('Organization'),
        'sort' => 'ec.organization',
        'direction' => CRM_Utils_Sort::ASCENDING,
      ];
    }
    $sortID = NULL;
    if ($this->get(CRM_Utils_Sort::SORT_ID)) {
      $sortID = CRM_Utils_Sort::sortIDValue($this->get(CRM_Utils_Sort::SORT_ID),
        $this->get(CRM_Utils_Sort::SORT_DIRECTION)
      );
    }
    $sort = new CRM_Utils_Sort($headers, $sortID);
    $this->assign_by_ref('headers', $headers);
    $this->assign_by_ref('sort', $sort);
    $this->set(CRM_Utils_Sort::SORT_ID,
      $sort->getCurrentSortID()
    );
    $this->set(CRM_Utils_Sort::SORT_DIRECTION,
      $sort->getCurrentSortDirection()
    );

    return $sort->orderBy();
  }

}
