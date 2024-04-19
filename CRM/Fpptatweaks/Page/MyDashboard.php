<?php
use CRM_Fpptatweaks_ExtensionUtil as E;

class CRM_Fpptatweaks_Page_MyDashboard extends CRM_Core_Page {

  public function run() {
    // This page will simply redirect to whatever base URL is defined in $_GET['dashbase'],
    // after appending the appropriate query string parameters for the given dashboard contact.
    // $_GET['dashbase'] was set as a base64 encoding of the live user dashboard url
    // in CRM_Contact_Page_View_UserDashBoard.js.
    
    // Start by decoding dashbase to an actual url.
    $dashbase = base64_decode($_GET['dashbase']);
    // Parse that url into parts.
    $dashbaseParts = parse_url($dashbase);
    
    // Use whatever query string parameters were contained in dashbase, but be sure
    // to change (or add) the parameters that civicrm will use to display the 
    // appropriate user dashboard.
    parse_str($dashbaseParts['query'], $queryParams);  
    $queryParams['reset'] = 1;
    $queryParams['id'] = $_GET['id'];
    
    // Append url punctuation to each url component, if that component exists.
    $scheme   = isset($dashbaseParts['scheme']) ? $dashbaseParts['scheme'] . '://' : '';
    $host     = isset($dashbaseParts['host']) ? $dashbaseParts['host'] : '';
    $port     = isset($dashbaseParts['port']) ? ':' . $dashbaseParts['port'] : '';
    $user     = isset($dashbaseParts['user']) ? $dashbaseParts['user'] : '';
    $pass     = isset($dashbaseParts['pass']) ? ':' . $dashbaseParts['pass']  : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($dashbaseParts['path']) ? $dashbaseParts['path'] : '';
    $query    = !empty($queryParams) ? '?' . http_build_query($queryParams) : '';
    $fragment = isset($dashbaseParts['fragment']) ? '#' . $dashbaseParts['fragment'] : '';

    // Assemble url components into an actual url.
    $url = "$scheme$user$pass$host$port$path$query$fragment";
  
    // Redirect to the resulting url.
    CRM_Utils_System::redirect($url);
  }

}
