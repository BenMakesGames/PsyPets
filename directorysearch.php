<?php
$child_safe = false;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

$search_distance = 120;

$profiles = array();
$profile_details = array();
$warnings = array();
$errors = array();

$userprofile = get_user_profile($user['idnum']);
if($userprofile == false)
  $distance_search = false;
else
  $distance_search = ($userprofile['locationsearch'] != 'no');

if($distance_search)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_profiles WHERE locationsearch=\'yes\'';
  $locationsearchable = $database->FetchSingle($command, 'counting location-searchable profiles');
}

if($_GET['action'] == 'search')
  $_POST = $_GET;

if($_POST['action'] == 'search')
{
  $url_get_params = '';
  $order_by = 'ORDER BY name ASC';

  $command = 'SELECT REPLACE_ME FROM monster_profiles AS p,monster_users AS u WHERE ';
  $criteria = array();

  $criteria[] = "p.idnum=u.idnum AND p.enabled='yes'";

  $_POST['name'] = trim($_POST['name']);
  if(strlen($_POST["name"]) > 0)
  {
    $url_get_params .= '&amp;name=' . $_POST['name'];
    if($_POST['exactname'] == 'yes' || $_POST['exactname'] == 'on')
    {
      $url_get_params .= '&amp;exactname=yes';
      $criteria[] = 'p.name LIKE ' . quote_smart($_POST['name']);
    }
    else
      $criteria[] = 'p.name LIKE ' . quote_smart('%' . $_POST['name'] . '%');
  }

  if($_POST['online'] == 'on' || $_POST['online'] == 'yes')
  {
    $url_get_params .= '&amp;online=yes';
    $criteria[] = 'u.lastactivity>=' . ($now - 5 * 60);
  }

  $_POST['screenname'] = trim($_POST["screenname"]);
  if(strlen($_POST['screenname']) > 0)
  {
    $url_get_params .= '&amp;screenname=' . $_POST['screenname'];
    $criteria[] = '(p.aim=' . quote_smart($_POST['screenname']) . ' OR ' .
                  'p.yahoo=' . quote_smart($_POST['screenname']) . ' OR ' .
                  'p.skype=' . quote_smart($_POST['screenname']) . ' OR ' .
                  'p.msn=' . quote_smart($_POST['screenname']) . ')';
  }

  if($_POST['gender'] == 'male' || $_POST['gender'] == 'female')
  {
    $url_get_params .= '&amp;gender=' . $_POST['gender'];
    $criteria[] = 'p.gender=' . quote_smart($_POST['gender']);
  }

  $minage = (int)$_POST['minage'];
  $maxage = (int)$_POST['maxage'];
  
  if($minage > 13)
  {
    $url_get_params .= '&amp;minage=' . $minage;
    $min_birthday = date('Y-m-d', $now - $minage * 60 * 60 * 24 * 365);
    $criteria[] = 'u.birthday<=' . quote_smart($min_birthday);
  }
  else
  {
    if($_POST['minage'] > 0)
      $warnings[] = '<p class="obstacle">The law prevents children under the age of 13 from signing up. If you lie, and are caught, your account will be deleted!</p>';
    $_POST['minage'] = '';
  }

  if($maxage > 13)
  {
    $url_get_params .= '&amp;maxage=' . $maxage;
    $max_birthday = date('Y-m-d', $now - $maxage * 60 * 60 * 24 * 365);
    $criteria[] = 'u.birthday>=' . quote_smart($max_birthday);
  }
  else
  {
    if($maxage > 0)
      $warnings[] = '<p class="obstacle">The law prevents children under the age of 13 from signing up. If you lie, and are caught, your account will be deleted!</p>';
    $_POST['maxage'] = '';
  }

  if($_POST['maxage'] < $_POST['minage'] && $_POST['maxage'] > 0)
    $errors[] = 'The maximum age can\'t be less than the minimum age...';

  if($distance_search && ($_POST['nearby'] == 'yes' || $_POST['nearby'] == 'on'))
  {
    $radius = $search_distance;
    $distance_lookup_slop_miles = 5;
    $latitude = $userprofile['latitude'];
    $longitude = $userprofile['longitude'];
  
    $delta_latitude = (($radius + $distance_lookup_slop_miles) / 69.172);
    $lat1 = $latitude + $delta_latitude;
    $lat2 = $latitude - $delta_latitude;
    
    $delta_longitude = (($radius + $distance_lookup_slop_miles) / (cos($latitude) * 69.172));
    $long1 = $longitude + $delta_longitude;
    $long2 = $longitude - $delta_longitude;

    $url_get_params .= '&amp;nearby=yes';    
    $criteria[] = 'p.locationsearch=\'yes\' AND p.latitude<=' . $lat1 . ' AND p.latitude>=' . $lat2 .
                  ' AND p.longitude<=' . $long1 . ' AND p.longitude>=' . $long2;
  }

  if(count($criteria) == 1)
    $errors[] = 'You must provide at least one piece of information to search with.';

  if(count($errors) == 0)
  {
    $command .= implode(' AND ', $criteria);
 
    $countcommand = str_replace('REPLACE_ME', 'COUNT(p.idnum) AS c', $command);
    $count_data = $database->FetchSingle($countcommand, 'directorysearch.php');

    $num_results = $count_data['c'];

    $num_pages = ceil($num_results / 20);
     
    $page = (int)$_GET['page'];
    if($page < 1 || $page > $num_pages)
      $page = 1;

    $pages = paginate($num_pages, $page, '/directorysearch.php?action=search' . $url_get_params . '&amp;page=%s');

    $command .= ' ' . $order_by . ' LIMIT ' . (($page - 1) * 20) . ',20';

    $command = str_replace('REPLACE_ME', 'p.*,u.lastactivity,u.openstore,u.display,u.donated,u.birthday', $command);

    if($num_results > 0)
    {
      $search_time = microtime(true);
      $profiles = $database->FetchMultiple($command);
      $search_time = microtime(true) - $search_time;
      $footer_note = '<br />Took ' . round($search_time, 4) . 's querying the DB.';
    }
    else
      $profiles = array();
  }
}

$report_command = $command;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Directory &gt; Search</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="directory.php">Directory</a> &gt; Search</h4>
<?php
if(count($warnings) > 0)
  echo '<ul><li>' . implode('</li><li>', $warnings) . '</li></ul>';

if(count($errors) > 0)
 echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';
else
{
?>
     <p>All the search fields are optional.  <strong>Remember:</strong> this does not search all of the Residents, only those who opted to be searchable (<a href="/myaccount/searchable.php">edit your searchable profile</a>).</p>
<?php
}
?>
     <form method="post" name="searchform" id="searchform">
     <table>
      <tr>
       <td bgcolor="#f0f0f0">Name:</td>
       <td><input name="name" maxlength=32 value="<?= $_POST["name"] ?>" /></td>
       <td>&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">&nbsp;</td>
       <td><input type="checkbox" name="exactname" <?= ($_POST["exactname"] == "yes") ? "checked" : "" ?> /> Match this name exactly</td>
       <td>Check this box if you want, for example, "alex" to find only "alex", and not "alex13", "alexander", etc.</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">&nbsp;</td>
       <td colspan=2>&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">Screen&nbsp;name:</td>
       <td valign="top"><input name="screenname" maxlength=32 value="<?= $_POST["screenname"] ?>" /></td>
       <td valign="top">Searches for a particular AIM, Yahoo!, MSN, or Skype screen name.</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">&nbsp;</td>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">Age:</td>
       <td><input name="minage" maxlength=3 value="<?= $_POST["minage"] ?>" size="3" /> to <input name="maxage" maxlength=3 value="<?= $_POST["maxage"] ?>" size="3" /></td>
       <td>You can fill in a minimum age, maximum age, or both.</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">&nbsp;</td>
       <td colspan=2>&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0" valign="top">Gender:</td>
       <td valign="top">
        <input type="radio" name="gender" value="any" checked /> Any<br />
        <input type="radio" name="gender" value="male"<?= $_POST["gender"] == "male" ? " checked" : "" ?> /> Male<br />
        <input type="radio" name="gender" value="female"<?= $_POST["gender"] == "female" ? " checked" : "" ?> /> Female
       </td>
       <td>&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0">&nbsp;</td>
       <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
       <td bgcolor="#f0f0f0" valign="top">Online:</td>
       <td valign="top"><input type="checkbox" name="online"<?= ($_POST['online'] == 'yes' || $_POST['online'] == 'on') ? " checked" : "" ?> /></td>
       <td>Check if you only want to find Residents who are currently on-line.</td>
      </tr>
<?php
if($distance_search)
{
?>
      <tr>
       <td bgcolor="#f0f0f0" valign="top">Nearby:</td>
       <td valign="top"><input type="checkbox" name="nearby"<?= ($_POST['nearby'] == 'yes' || $_POST['nearby'] == 'on') ? ' checked' : '' ?> /></td>
       <td>Check if you only want to find Residents who are within about <?= $search_distance ?> miles (approximately a <?= round($search_distance / 60, 1) ?> hour drive).<br /><i>(<?= $locationsearchable['c'] ?> residents have provided their location for "nearby" search.)</i></td>
      </tr>
<?php
}
?>
      <tr>
       <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
       <td colspan="2" align="center">
        <input type="hidden" name="action" value="search">
        <input type="submit" value="Search" style="width:100px;">
       </td>
      </tr>
     </table>
     </form>
<?php
if($_POST['action'] == 'search')
{
  echo '<hr>';

  $profile_count = count($profiles);

  if($profile_count > 0)
  {
    echo '<p>Found ' . $num_results . ' matching resident' . ($num_results != 1 ? 's' : '') . '.</p>';

    if($num_pages > 1)
      echo $pages;
?>
     <table>
      <thead>
       <tr>
        <th></th>
        <th></th>
        <th>Resident&nbsp;Name</th>
        <th>Age</th>
        <th>Location</th>
       </tr>
      </thead>
      <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($profiles as $profile)
    {
      $command = 'SELECT user FROM monster_admins WHERE `user`=' . quote_smart($profile['user']) . ' LIMIT 1';
      $theadmin = $database->FetchSingle($command, 'directorysearch.php');

      $donator = $profile['donated'];
?>
      <tr class="<?= $rowclass ?>">
       <td><?php
    echo (($theadmin['admintag'] == 'yes')
         ? '<a href="admincontact.php"><img src="gfx/admintag.gif" width="16" height="16" alt="Administrator" border="0" /></a>'
         : '<img src="gfx/shim.gif" width="16" height="16" alt="" />') .

         (($donator == 'yes')
         ? '<img src="gfx/donator.gif" width="16" height="16" alt="bought favors" />'
         : '<img src="gfx/shim.gif" width="16" height="16" alt="" />') .

         (($profile['openstore'] == 'yes')
         ? '<a href="userstore.php?user=' . link_safe($profile['name']) . '"><img src="gfx/forsale.png" width="16" height="16" border="0" alt="visit store" /></a>'
         : '<img src="gfx/shim.gif" width="16" height="16" alt="" />');
?></td>
 <td><?php
  if($profile['gender'] == 'male')
    echo '<img src="gfx/boy.gif" height="12" width="12" alt="(male)" />';
  else if($profile['gender'] == 'female')
    echo '<img src="gfx/girl.gif" height="12" width="12" alt="(female)" />';

  $age = birthdate_to_age($profile['birthday']);
?></td>
       <td><a href="/residentprofile.php?resident=<?= link_safe($profile['name']) ?>"><?= $profile['name'] ?></a></td>
       <td align="center"><?= (($age <= 13 || $person['show_age'] == 'no') ? '-' : $age) ?></td>
       <td><?= $profile["location"] ?></td>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
      </tbody>
     </table>
<?php
    if($num_pages > 1)
      echo $pages;
  }
  else
    echo '<p>No profiles matched your search.</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
