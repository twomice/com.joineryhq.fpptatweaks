<?php

use CRM_Fpptatweaks_ExtensionUtil as E;

return array(
  'fpptatweaks_cppt_history_profile' => array(
    'group_name' => 'Fpptatweaks Use Tabs',
    'group' => 'fpptatweaks',
    'name' => 'fpptatweaks_cppt_history_profile',
    'add' => '5.0',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('Which profile should display CPPT History; this list contains only profiles marked "%1".', [1 => E::ts('Display on Contact Dashboard?', ['domain' => 'com.joineryhq.cdashtabs'])] ),
    'title' => E::ts('CPPT History Profile'),
    'type' => 'Int',
    'quick_form_type' => 'Element',
//    'default' => 1,
    'html_type' => 'Select',
    'html_attributes' => array(
      'class' => 'crm-select2',
      'style' => "width:auto;",
    ),
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getCpptHistoryProfileOptions'
  ),
  'fpptatweaks_new_relationship_profile' => array(
    'group_name' => 'New Relationship Profile',
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
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getUFGroupList'
  ),
  'fpptatweaks_new_relationship_tag' => array(
    'group_name' => 'New Relationship Tag',
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
    'X_options_callback' => 'CRM_Fpptatweaks_Form_Settings::getTagList'
  ),
 );
