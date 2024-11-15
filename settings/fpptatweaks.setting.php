<?php

use CRM_Fpptatweaks_ExtensionUtil as E;

$settings = array(
  'fpptatweaks_cppt_history_profile' => array(
    'group_name' => 'domain',
    'group' => 'fpptatweaks',
    'name' => 'fpptatweaks_cppt_history_profile',
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('Which profile should display CPPT History; this list contains only profiles marked "%1".', [1 => E::ts('Display on Contact Dashboard?')]),
    'title' => E::ts('CPPT History Profile'),
    'type' => 'Int',
    'quick_form_type' => 'Element',
//    'default' => 1,
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
      'style' => "width:auto;",
    ),
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getCpptHistoryProfileOptions',
  ),
  'fpptatweaks_new_relationship_profile' => array(
    'group_name' => 'domain',
    'group' => 'fpptatweaks',
    'name' => 'fpptatweaks_new_relationship_profile',
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'title' => E::ts('New Relationship Profile'),
    'type' => 'Int',
    'quick_form_type' => 'Element',
//    'default' => 1,
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
      'style' => "width:auto;",
    ),
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getUFGroupList',
  ),
  'fpptatweaks_new_relationship_tag' => array(
    'group_name' => 'domain',
    'group' => 'fpptatweaks',
    'name' => 'fpptatweaks_new_relationship_tag',
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => '',
    'title' => E::ts('New Relationship Tag'),
    'type' => 'Int',
    'quick_form_type' => 'Element',
//    'default' => 1,
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
      'style' => "width:auto;",
    ),
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getTagList',
  ),
  'fpptatweaks_dashboard_url' => array(
    'group_name' => 'domain',
    'group' => 'fpptatweaks',
    'name' => 'fpptatweaks_dashboard_url',
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Must begin with http:// or https://',
    'title' => E::ts('Full URL to "My Dashboard" page'),
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'Text',
  ),
);

foreach (CRM_Fpptatweaks_Util::getSupportedDashboardSectionAppends() as $name => $label) {
  $settingName = 'fpptatweaks_dashboard_section_post_' . $name;
  $settings[$settingName] = array(
    'group_name' => 'domain',
    'group' => 'fpptatweaks',
    'name' => $settingName,
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'title' => E::ts('HTML to append in Dashboard <em>' . $label . '</em> section'),
    'type' => 'String',
    'quick_form_type' => 'Element',
    'html_type' => 'Textarea',
    'html_attributes' => array(
      'rows' => '10',
      'cols' => "70",
      'class' => 'crm-form-wysiwyg',
    ),
  );
}

return $settings;
