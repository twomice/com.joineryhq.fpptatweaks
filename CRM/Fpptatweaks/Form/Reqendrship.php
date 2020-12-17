<?php

use CRM_Fpptatweaks_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Fpptatweaks_Form_Reqendrship extends CRM_Core_Form {

  public function buildQuickForm() {
    if (!$this->_flagSubmitted) {
      if ($_SERVER['HTTP_REFERER']) {
        // Try to return to referrer.
        $this->set('destination', $_SERVER['HTTP_REFERER']);
      }

      $rid = (CRM_Utils_Request::retrieve('rid', 'Int') ?? 0);
      if (!$rid) {
        CRM_Core_Error::statusBounce('Expected relationship ID in URL parameter "rid" but found none.');
      }
      $this->set('rid', $rid);

      $relationship = \Civi\Api4\Relationship::get()
        ->addWhere('id', '=', $rid)
        ->addChain('contact_b', \Civi\Api4\Contact::get()
          ->addWhere('id', '=', '$contact_id_b'),
          0)
        ->addChain('contact_a', \Civi\Api4\Contact::get()
          ->addWhere('id', '=', '$contact_id_a'),
          0)
        ->addChain('relationship_type', \Civi\Api4\RelationshipType::get()
          ->addWhere('id', '=', '$relationship_type_id'),
          0)
        ->execute()
        ->first();
      if (!$relationship) {
        CRM_Core_Error::statusBounce('No such relationship could be found.');
      }
      // Store values for postProcess.
      $this->set('relationship_type', $relationship['relationship_type']['label_b_a']);
      $this->set('contact_id_a', $relationship['contact_id_a']);
      $this->set('contact_id_b', $relationship['contact_id_b']);
      $this->set('contact_name_a', $relationship['contact_a']['display_name']);
      $this->set('contact_name_b', $relationship['contact_b']['display_name']);

      // Pass values to template for display.
      $this->assign('contact_name_b', $relationship['contact_b']['display_name']);
      $this->assign('contact_name_a', $relationship['contact_a']['display_name']);
      $this->assign('relationship_type', $relationship['relationship_type']['label_b_a']);
    }

    // add form elements
    $this->add(
      'textarea', // field type
      'comment', // field name
      E::ts('Comment'), // field label
      TRUE // is required
    );
    $this->addButtons(array(
      array(
        'type' => 'done',
        'name' => E::ts('Submit request'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();
    // create "end relationship request" activity
    $activityTypeOptionValue = \Civi\Api4\OptionValue::get()
      ->addWhere('option_group_id', '=', 2)
      ->addWhere('name', '=', 'Request relationship end')
      ->execute()
      ->first();

    $subject = E::ts('User requests: remove %1 (%2 / %3)', [
      '1' => $this->get('relationship_type'),
      '2' => $this->get('contact_name_a'),
      '3' => $this->get('contact_name_b'),
    ]);
    $results = \Civi\Api4\Activity::create()
      ->addValue('activity_type_id', $activityTypeOptionValue['value'])
      ->addValue('End_relationship_details.Contact_A', $this->get('contact_id_a'))
      ->addValue('End_relationship_details.Contact_B', $this->get('contact_id_b'))
      ->addValue('details', $values['comment'])
      ->addValue('subject', $subject)
      ->addValue('End_relationship_details.Relationship_type_name', $this->get('relationship_type'))
      ->addValue('source_contact_id', CRM_Core_Session::getLoggedInContactID())
      ->execute();

    CRM_Core_Session::setStatus($subject, E::ts('Request filed, pending review by FPPTA staff'));

    // Redirect to destination if known.
    if ($destination = $this->get('destination')) {
      CRM_Core_Session::singleton()->pushUserContext($destination);
    }
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
