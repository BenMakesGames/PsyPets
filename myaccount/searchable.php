<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

$userprofile = get_user_profile($user['idnum']);

if($_POST['action'] == 'updatesearchable')
{
  if($_POST['enabled'] == 'yes' || $_POST['enabled'] == 'on')
    $userprofile['enabled'] = 'yes';
  else
    $userprofile['enabled'] = 'no';

  $userprofile['gender'] = $_POST['gender'];
  $userprofile['location'] = $_POST['location'];

  $userprofile['show_age'] = (($_POST['show_age'] == 'yes' || $_POST['show_age'] == 'on') ? 'yes' : 'no');

  $userprofile['aim'] = str_replace(' ', '', $_POST['aim']);
  $userprofile['yahoo'] = str_replace(' ', '', $_POST['yahoo']);
  $userprofile['msn'] = str_replace(' ', '', $_POST['msn']);
  $userprofile['skype'] = str_replace(' ', '', $_POST['skype']);

  $userprofile['url'] = trim($_POST['url']);
  $userprofile['facebook'] = trim($_POST['facebook']);
  $userprofile['myspace'] = trim($_POST['myspace']);

  $userprofile['name'] = $user['display'];

  $codetype = $_POST['codetype'];
  $code = trim($_POST['code']);

  if($_POST['locationsearch'] == 'yes' || $_POST['locationsearch'] == 'on')
    $userprofile['locationsearch'] = 'yes';
  else
    $userprofile['locationsearch'] = 'no';

  $lat = strtoupper(trim($_POST['latitude']));
  $lon = strtoupper(trim($_POST['longitude']));
  
  if(substr($lat, -1) == 'S')
    $userprofile['latitude'] = -(float)$_POST['latitude'];
  else
    $userprofile['latitude'] = (float)$_POST['latitude'];

  if(substr($lon, -1) == 'W')
    $userprofile['longitude'] = -(float)$_POST['longitude'];
  else
    $userprofile['longitude'] = (float)$_POST['longitude'];

  if(strlen($code) > 0)
  {
    if($codetype == 'us')
    {
      $command = 'SELECT `lat`,`lon` FROM zip_codes WHERE zip_code=' . (int)$code . ' LIMIT 1';
      $zip = fetch_single($command, 'fetching latitude/longitude');
    }
    else if($codetype == 'au')
    {
      $command = 'SELECT `lat`,`lon` FROM au_postcodes WHERE postcode=' . (int)$code . ' LIMIT 1';
      $zip = fetch_single($command, 'fetching latitude/longitude');
    }
    else if($codetype == 'uk')
    {
      if(strlen($code) > 3)
      {
        $i = strpos($code, ' ');
        if($i !== false)
        {
          $code2 = substr($code, 0, $i);

          $error_message .= '<p class="progress">Trying ' . $code2 . ', instead...</p>';

          $command = 'SELECT `lat`,`lon` FROM uk_postcodes WHERE postcode=' . quote_smart($code2) . ' LIMIT 1';
          $zip = fetch_single($command, 'fetching latitude/longitude');
        }
        else
          $error_message .= '<p class="failure">Having trouble locating that UK postcode.  Try entering the complete postcode, with a space.</p>';
      }
      else
      {
        $command = 'SELECT `lat`,`lon` FROM uk_postcodes WHERE postcode=' . quote_smart($code) . ' LIMIT 1';
        $zip = fetch_single($command, 'fetching latitude/longitude');
      }
    }
    else if($codetype == 'ca')
    {
      if(strlen($code) > 3)
      {
        $i = strpos($code, ' ');
        if($i !== false)
        {
          $code2 = substr($code, 0, $i);

          $error_message .= '<p class="progress">Trying ' . $code2 . ', instead...</p>';

          $command = 'SELECT `lat`,`lon` FROM ca_postcodes WHERE postcode=' . quote_smart($code2) . ' LIMIT 1';
          $zip = fetch_single($command, 'fetching latitude/longitude');
        }
        else
          $error_message .= '<p class="failure">Having trouble locating that CA postcode.  Try entering the complete postcode, with a space.</p>';
      }
      else
      {
        $command = 'SELECT `lat`,`lon` FROM ca_postcodes WHERE postcode=' . quote_smart($code) . ' LIMIT 1';
        $zip = fetch_single($command, 'fetching latitude/longitude');
      }
    }
    else
    {
      $error_message .= '<p class="failure">Unrecognized country was specified.</p>';
      $zip = false;
    }

    if($zip !== false)
    {
      $userprofile['latitude'] = $zip['lat'];
      $userprofile['longitude'] = $zip['lon'];

      if(!$code2)
        $code2 = $code;

      $error_message .= '<p class="success">Found ' . strtoupper($codetype) . ' code ' . $code2 . ' at (' . $zip['lat'] . ', ' . $zip['lon'] . ').</p>';
    }
    else
      $error_message .= '<p class="failure">' . $SETTINGS['site_name'] . ' could not find ' . strtoupper($codetype) . ' code ' . $code . '.</p>';
  }

  if($userprofile['latitude'] == 0 && $userprofile['longitude'] == 0)
    $userprofile['locationsearch'] = 'no';

  save_user_profile($userprofile);
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Searchable Profile</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   $(function() {
     if(navigator.geolocation)
       ;
     else
     {
       $('#geolocation_button').attr('disabled', true);
       $('#geolocation_desc').append('  <span class="failure">Your browser does not support this feature.</span>');
     }
   });

   function GeolocationLookup()
   {
    if(navigator.geolocation)
    {
      navigator.geolocation.getCurrentPosition(function(position) {
        $('#geo_lat').val(position.coords.latitude);
        $('#geo_long').val(position.coords.longitude);
        
        $('#geolocation_desc').html('<span class="success">Success!  Remember to "Update Profile" when you\'re all done.</span>');
      });
    }
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myaccount/">My Account</a> &gt; Searchable Profile</h4>
     <ul class="tabbed">
      <li><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
      <li class="activetab"><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
      <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
      <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
      <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
      <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
      <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
      <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Filtering</a></li>
     </ul>
<?php
if($general_message)
  echo "     <p style=\"color:blue;\">" . $general_message . "</p>\n";

if($error_message)
  echo $error_message;
?>
     <ul>
      <li><a href="directorysearch.php">Find other searchable Residents</a></li> 
     </ul>
     <table>
      <form method="post" id="account" name="account">
      <tr class="titlerow">
       <td colspan="2" align="center"><h4>Searchable&nbsp;Profile</td>
       <td></td>
      </tr>
      <tr>
       <td class="leftbar" valign="top">Enabled:</td>
       <td valign="top"><input type="checkbox" name="enabled" <?= ($userprofile['enabled'] == 'yes') ? 'checked' : '' ?> /></td>
       <td valign="top">
        Leave this box unchecked if you do not want other residents searching for you.<br />
        All the information on this form is optional.<br />
       </td>
      </tr>
      <tr>
       <td class="leftbar">Show Age:</td>
       <td><input type="checkbox" name="show_age"<?= $userprofile['show_age'] == 'yes' ? ' checked="checked"' : '' ?> /></td>
       <td>Math suggests you're <?= birthdate_to_age($user['birthday']) ?>.</td>
      </tr>
      <tr>
       <td class="leftbar"></td>
       <td colspan="2"></td>
      </tr>
      <tr>
       <td class="leftbar" valign="top">Gender:</td>
       <td valign="top">
        <input type="radio" name="gender" value="none"<?= ($userprofile['gender'] == 'none' ? ' checked' : '') ?> />Unspecified<br />
        <input type="radio" name="gender" value="male"<?= ($userprofile['gender'] == 'male' ? ' checked' : '') ?> />Male<br />
        <input type="radio" name="gender" value="female"<?= ($userprofile['gender'] == 'female' ? ' checked' : '') ?> />Female<br />
       </td>
       <td valign="top"></td>
      </tr>
      <tr>
       <td class="leftbar"></td>
       <td colspan="2"></td>
      </tr>
      <tr>
       <td class="leftbar">Location:</td>
       <td><input name="location" maxlength="64" value="<?= $userprofile['location'] ?>" /></td>
       <td>
       </td>
      </tr>
      <tr>
       <td class="leftbar"></td>
       <td colspan="2"></td>
      </tr>
      <tr>
       <td class="leftbar"><nobr>Facebook Profile:</nobr></td>
       <td colspan="2">
        <span class="textbox"><nobr>http://www.facebook.com/<input name="facebook" maxlength="32" value="<?= $userprofile['facebook'] ?>" /></nobr></span>
       </td>
      </tr>
      <tr>
       <td class="leftbar"><nobr>MySpace Profile:</nobr></td>
       <td colspan="2">
        <span class="textbox"><nobr>http://www.myspace.com/<input name="myspace" maxlength="32" value="<?= $userprofile['myspace'] ?>" /></nobr></span>
       </td>
      </tr>
      <tr>
       <td class="leftbar">Web page:</td>
       <td colspan="2">
        <span class="textbox"><nobr>http://<input name="url" maxlength="128" type="text" value="<?= $userprofile['url'] ?>" /></nobr></span>
       </td>
      </tr>
      <tr>
       <td class="leftbar"></td>
       <td colspan="2"></td>
      </tr>
      <tr>
       <td class="leftbar">AIM:</td>
       <td><input name="aim" maxlength="64" value="<?= $userprofile['aim'] ?>" /></td>
       <td>Enter any screen names you like, separated with commas.</td>
      </tr>
      <tr>
       <td class="leftbar">Yahoo!:</td>
       <td><input name="yahoo" maxlength="64" value="<?= $userprofile['yahoo'] ?>" /></td>
       <td>For example: "myname1, myname2,myname3"</td>
      </tr>
      <tr>
       <td class="leftbar">MSN:</td>
       <td><input name="msn" maxlength="64" value="<?= $userprofile['msn'] ?>" /></td>
       <td></td>
      </tr>
      <tr>
       <td class="leftbar">Skype:</td>
       <td><input name="skype" maxlength="64" value="<?= $userprofile['skype'] ?>" /></td>
       <td></td>
      </tr>
      <tr>
       <td colspan="3" align="center" style="padding-top: 1em;">
        <input type="hidden" name="action" value="updatesearchable" />
        <input type="submit" name="submit" value="Update Profile" style="width:120px;" />
       </td>
      </tr>
      <tr>
       <td colspan="3">&nbsp;</td>
      </tr>
      <tr class="titlerow">
       <td colspan="2" align="center"><h4>Location&nbsp;Search</td>
       <td></td>
      </tr>
      <tr>
       <td class="leftbar" valign="top">Enabled:</td>
       <td valign="top"><input type="checkbox" name="locationsearch" <?= ($userprofile['locationsearch'] == 'yes') ? 'checked' : '' ?> /></td>
       <td valign="top">
        "Location Search" allows Residents to <a href="directorysearch.php">find each other based on location</a>.  Exact locations are never revealed, only that two Residents are "nearby" to each other. Note: Even if Location Search is enabled, you cannot be searched for at all if your basic Searchable Profile is not also enabled.
       </td>
      </tr>
      <tr>
       <td class="leftbar"><nobr>Geolocation:</nobr></td>
       <td><button id="geolocation_button" onclick="GeolocationLookup(); return false;"> Look Up </button>
       </td>
       <td><span id="geolocation_desc">Uses geolocation features in your browser to guess your location.</span></td>
      </tr>
      <tr>
       <td class="leftbar">Latitude:</td>
       <td><input name="latitude" id="geo_lat" maxlength="8" size="8" value="<?= $userprofile['latitude'] != 0 ? $userprofile['latitude'] : '' ?>" /></td>
       <td>If you cannot or do not want to use the Geolocation Look Up feature, you can enter your latitude and longitude manually.</td>
      </tr>
      <tr>
       <td class="leftbar">Longitude:</td>
       <td><input name="longitude" id="geo_long" maxlength="8" size="8" value="<?= $userprofile['longitude'] != 0 ? $userprofile['longitude'] : '' ?>" /></td>
       <td></td>
      </tr>
      <tr>
       <td colspan="3" align="center" style="padding-top: 1em;">
        <input type="hidden" name="action" value="updatesearchable" />
        <input type="submit" name="submit" value="Update Profile" style="width:120px;" />
       </td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
