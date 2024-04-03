# CiviCRM: FPPTA CiviCRM Tweaks (com.joineryhq.fpptatweaks)

Specialized CiviCRM modifications for FPPTA:

* Provide a Settings page at Administer > Customize Data and Screens > FPPTA Tweaks Settings, with these settings:
  * CPPT History Profile: (Requires cdashtabs extension) Select an existing CiviCRM profile to be used as the display content for the "CPPT History" tab on a tabbed contact dashboard.
  * New Relationship Profile: Select an existing CiviCRM profile for use in requesting creation of a new organization/individual relationship.
  * New Relationship Tag: Select an existing tag to be applie to individual contacts for whom a new organization/individual relationship has been requested.
* Provide a "Request End Relationship" form for requesting removal of an existing organization/individual relationship.
* On the CiviCRM user dashboard (https://drupal.example.org/civicrm/user):
  * Under the "Your Contacts / Organizations" section:
	* Replace all clickable links with equivalent non-clickable plain text in the "Relationship" column and the (unlabeled) Contact Name column.
	* Replace the "Disable" link in the (unlabeled) Actions column with a "Remove" link which points to the "Request End Relationship" form.
  * Under the "Your Contribution(s)" section:
    * Replace "Financial Type" with "Source"
* Provide a new participant listing type: "Name and Organization".
* Add Invoice Number column in event and contribution search.
* Remove default values for 'status' field in search criteria on Find Contributions.

The extension is licensed under [GPL-3.0](LICENSE.txt).

## Requirements

* PHP v7.0+
* CiviCRM 5.0+

## Installation 

Please follow [the usual instructions for installing a CiviCRM extension](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/#installing-a-new-extension).

## Usage

No configuration is necessary. This extension performs its work automatically when enabled.

## Support
![Joinery logo](/images/joinery-logo.png)

Joinery provides services for CiviCRM including custom extension development, 
training, data migrations, and more. We aim to keep this extension in good 
working order, and will do our best to respond appropriately to issues reported 
on its [github issue queue](https://github.com/twomice/com.joineryhq.fpptatweaks/issues). 
In addition, if you require urgent or highly customized improvements to this 
extension, we may suggest conducting a fee-based project under our standard 
commercial terms.  In any case, the place to start is the 
[github issue queue](https://github.com/twomice/com.joineryhq.fpptatweaks/issues) 
-- let us hear what you need and we'll be glad to help however we can.

And, if you need help with any other aspect of CiviCRM -- from hosting to custom 
development to strategic consultation and more -- please contact us directly via 
https://joineryhq.com
