<?php

require_once 'CRM/Core/Form.php';
use CRM_Fpptatweaks_ExtensionUtil as E;

/**
 * Form controller class for extension Settings form.
 * Borrowed heavily from
 * https://github.com/eileenmcnaughton/nz.co.fuzion.civixero/blob/master/CRM/Civixero/Form/XeroSettings.php
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Fpptatweaks_Form_Settings extends CRM_Core_Form {

  public static $settingFilter = array('group' => 'fpptatweaks');
  public static $extensionName = 'com.joineryhq.fpptatweaks';
  private $_submittedValues = array();
  private $_settings = array();

  public function __construct(
    $state = NULL,
    $action = CRM_Core_Action::NONE,
    $method = 'post',
    $name = NULL
  ) {

    $this->setSettings();

    parent::__construct(
      $state = NULL,
      $action = CRM_Core_Action::NONE,
      $method = 'post',
      $name = NULL
    );
  }

  public function buildQuickForm() {
    $settings = $this->_settings;
    foreach ($settings as $name => $setting) {
      if (isset($setting['quick_form_type'])) {
        switch ($setting['html_type']) {
          case 'Select':
            $this->add(
              // field type
              $setting['html_type'],
              // field name
              $setting['name'],
              // field label
              $setting['title'],
              $this->getSettingOptions($setting),
              NULL,
              $setting['html_attributes']
            );
            break;

          case 'CheckBox':
            $this->addCheckBox(
              // field name
              $setting['name'],
              // field label
              $setting['title'],
              array_flip($this->getSettingOptions($setting))
            );
            break;

          case 'Radio':
            $this->addRadio(
              // field name
              $setting['name'],
              // field label
              $setting['title'],
              $this->getSettingOptions($setting)
            );
            break;

          default:
            $add = 'add' . $setting['quick_form_type'];
            if ($add == 'addElement') {
              $this->$add($setting['html_type'], $name, E::ts($setting['title']), CRM_Utils_Array::value('html_attributes', $setting, array()));
            }
            else {
              $this->$add($name, E::ts($setting['title']));
            }
            break;
        }
      }
      $descriptions[$setting['name']] = E::ts($setting['description']);

      if (!empty($setting['X_form_rules_args'])) {
        $rules_args = (array) $setting['X_form_rules_args'];
        foreach ($rules_args as $rule_args) {
          array_unshift($rule_args, $setting['name']);
          call_user_func_array(array($this, 'addRule'), $rule_args);
        }
      }
    }
    $this->assign("descriptions", $descriptions);

    $this->addButtons(array(
      array(
        'type' => 'done',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ),
    ));

    $style_path = CRM_Core_Resources::singleton()->getPath(self::$extensionName, 'css/extension.css');
    if ($style_path) {
      CRM_Core_Resources::singleton()->addStyleFile(self::$extensionName, 'css/extension.css');
    }

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());

    $session = CRM_Core_Session::singleton();
    $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/fpptatweaks/settings', "reset=1"));
    parent::buildQuickForm();
  }

  public function postProcess() {
    $this->_submittedValues = $this->exportValues();
    $this->saveSettings();
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons". These
    // items don't have publiclabels. We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  /**
   * Define the list of settings we are going to allow to be set on this form.
   *
   */
  public function setSettings() {
    if (empty($this->_settings)) {
      $this->_settings = self::getSettings();
    }
  }

  public static function getSettings() {
    $settings = _fpptatweaks_civicrmapi('setting', 'getfields', array('filters' => self::$settingFilter));
    return $settings['values'];
  }

  /**
   * Get the settings we are going to allow to be set on this form.
   *
   */
  public function saveSettings() {
    $settings = $this->_settings;
    $values = array_intersect_key($this->_submittedValues, $settings);
    _fpptatweaks_civicrmapi('setting', 'create', $values);

    // Save any that are not submitted, as well (e.g., checkboxes that aren't checked).
    $unsettings = array_fill_keys(array_keys(array_diff_key($settings, $this->_submittedValues)), NULL);
    _fpptatweaks_civicrmapi('setting', 'create', $unsettings);

    CRM_Core_Session::setStatus(" ", E::ts('Settings saved.'), "success");
  }

  /**
   * Set defaults for form.
   *
   * @see CRM_Core_Form::setDefaultValues()
   */
  public function setDefaultValues() {
    $result = _fpptatweaks_civicrmapi('setting', 'get', array('return' => array_keys($this->_settings)));
    $domainID = CRM_Core_Config::domainID();
    $ret = CRM_Utils_Array::value($domainID, $result['values']);
    return $ret;
  }

  public function getSettingOptions($setting) {
    if (!empty($setting['X_options_callback']) && is_callable($setting['X_options_callback'])) {
      return call_user_func($setting['X_options_callback']);
    }
    else {
      return CRM_Utils_Array::value('X_options', $setting, array());
    }
  }

  public static function getCpptHistoryProfileOptions() {
    if (is_callable('CRM_Cdashtabs_Settings::getFilteredSettings')) {
      $options = [
        '' => '-' . E::ts('none') . '-',
      ];
      $filteredUFGroupSettings = CRM_Cdashtabs_Settings::getFilteredSettings(TRUE, 'uf_group');
      foreach ($filteredUFGroupSettings as $filteredUFGroupSetting) {
        $gid = $filteredUFGroupSetting['uf_group_id'];
        $ufGroupResult = civicrm_api3('UFGroup', 'get', [
          'sequential' => 1,
          'return' => ["frontend_title", "title"],
          'id' => $gid,
        ]);
        $ufGroup = $ufGroupResult['values'][0];
        $options[$gid] = $ufGroup['frontend_title'] ?? $ufGroup['title'];
      }
    }
    else {
      $options = [
        '' => '-' . E::ts('N/A: requires Contact Dashboard Tabs extension') . '-',
      ];
    }
    return $options;

  }

  /**
   * Get UF Group list that used for Profile (Stand alone or Directory)
   *
   */
  public static function getUFGroupList() {
    $options = [
      '' => '-' . E::ts('none') . '-',
    ];
    // Call all ufgroup
    $uFGroups = \Civi\Api4\UFGroup::get()
      ->setCheckPermissions(FALSE)
      ->execute();
    foreach ($uFGroups as $ufGroup) {
      // Check if the join module is profile
      $ufJoinRecords = CRM_Core_BAO_UFGroup::getUFJoinRecord($ufGroup['id']);
      foreach ($ufJoinRecords as $key => $value) {
        if ($value == 'Profile') {
          // Add in the list of option if its profile
          $options[$ufGroup['id']] = $ufGroup['frontend_title'] ?? $ufGroup['title'];
          continue;
        }
      }
    }
    return $options;
  }

  /**
   * Get Tag List
   *
   */
  public static function getTagList() {
    $options = ['' => E::ts('- none -')] + CRM_Core_BAO_EntityTag::buildOptions('tag_id');
    return $options;
  }

}
