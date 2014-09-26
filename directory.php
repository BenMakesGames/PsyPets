<?php
$child_safe = false;
$require_petload = 'no';
$require_login = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($_GET['page'] < 1)
  $page = 1;
else
  $page = (int)$_GET['page'];

if(strlen($_GET['letter']) != 1)
  $letter = 'a';
else if(preg_match('/[^a-zA-Z]/', $_GET['letter']))
  $letter = 'a';
else
  $letter = $_GET['letter'];

if($user['idnum'] > 0)
{
  $userprofile = get_user_profile($user['idnum']);

  if($userprofile == false)
    $distance_search = false;
  else
    $distance_search = ($userprofile['locationsearch'] != 'no');
}
else
  $distance_search = false;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Directory &gt; <?= strtoupper($letter) ?></title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Directory &gt; <?= strtoupper($letter) ?></h4>
<img src="gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php
include 'commons/dialog_open.php';

if($_GET['dialog'] == 2)
{
?>
     <p><img src="gfx/admintag.gif" width="16" height="16" alt="(administrator)" class="inlineimage" /> indicates that the Resident is an Administrator.</p>
     <p><img src="gfx/donator.gif" width="16" height="16" alt="(paid)" class="inlineimage" /> indicates that the Resident has purchased <a href="wherethemoneygoes.php">Favor</a>.</p>
     <p><img src="gfx/forsale.png" width="16" height="16" alt="(store)" class="inlineimage" /> indicates the the Resident has an open store in the <a href="/fleamarket/">Flea Market</a>.  Click on this symbol to immediately visit it.</p>
<?php
}
else
  echo'     <p>This is the public resident directory.  It is an "opt-in" directory, meaning that residents are not listed here until they tell us that they\'d like to be.</p>';

include 'commons/dialog_close.php';

if($user['idnum'] > 0)
{
  echo '
    <ul>
     <li><a href="/directorysearch.php">Search this directory</a></li>
     <li><a href="/directorysearch.php?action=search&online=yes">Search for on-line Residents</a></li>
  ';

  if($distance_search)
    echo '      <li><a href="/directorysearch.php?action=search&nearby=yes">Search for nearby Residents</a></li>';

  echo '</ul>';
}

echo '<ul>';

if($_GET['dialog'] != 2)
  echo '<li><a href="/directory.php?letter=' . $letter . '&page=' . $page . '&dialog=2">Explain the icons used</a></li>';

if($user['idnum'] > 0)
  echo '<li><a href="/myaccount/searchable.php">Edit my listing</a></li>';

echo '</ul>';

if($message) echo '<p class="failure">' . $message . '</p>';

echo '<ul class="tabbed">';

$lval = strtoupper($letter);

$letters = explode(' ', 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z');

foreach($letters as $i=>$a)
{
  if($lval != $a)
    echo '<li><a href="directory.php?letter=' . strtolower($a) . '">' . $a . '</a></li> ';
  else
    echo '<li class="activetab"><a>' . $a . '</a></li> ';
}

echo '</ul>';

$sortby = 'display';
$sortorder = 'ASC';

$command  = 'SELECT REPLACE_ME ' .
            'FROM `monster_profiles` AS a LEFT JOIN monster_users AS b ON a.idnum=b.idnum ' .
            "WHERE a.enabled='yes' AND b.display LIKE '$letter%'";

$count_command = str_replace('REPLACE_ME', 'COUNT(a.idnum) AS qty', $command);

$data = $database->FetchSingle($count_command);
$count = $data['qty'];

$num_pages = ceil($count / 20);
if($page > $num_pages)
  $page = $num_pages;

$first = ($page - 1) * 20;

$command .= ' ORDER BY b.display ASC LIMIT ' . $first . ',20';

$command = str_replace('REPLACE_ME', 'a.*,b.birthday,b.display,b.user,b.openstore,b.donated', $command);

$people = $database->FetchMultiple($command, 'fetching residents');

$pages = paginate($num_pages, $page, "directory.php?letter=$letter&amp;page=%s");
?>
     <?= $pages ?>
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>Resident</th>
<?php
if($user['idnum'] > 0)
  echo '<th></th><th></th><th>Location</th>';

echo '</tr>';

$count = 0;

$rowclass = begin_row_class();

if($user['idnum'] > 0)
  $profile_url = '/residentprofile.php';
else
  $profile_url = '/publicprofile.php';

foreach($people as $person)
{
/*
  $count++;
  if($count == 21)
    break;*/

	$theadmin = $database->FetchSingle(
		"SELECT * " .
    "FROM monster_admins " .
    "WHERE `user`=" . quote_smart($person["user"]) . " LIMIT 1"
	);

  $donator = $person['donated'];
?>
<tr class="<?= $rowclass ?>">
 <td><?php
    if($theadmin['admintag'] == 'yes')
      echo '<a href="/admincontact.php"><img src="/gfx/admintag.gif" width="16" height="16" alt="(administrator)" /></a>';
    else if($donator == 'yes')
      echo '<img src="/gfx/donator.gif" width="16" height="16" alt="(paid account)" />';
?></td>
 <td><?php
    if($person["openstore"] == "yes")
      echo '<a href="/userstore.php?user=' . link_safe($person["display"]) . '"><img src="/gfx/forsale.png" width="16" height="16" alt="(store)" /></a>'
?></td>
 <td><a href="<?= $profile_url ?>?resident=<?= link_safe($person['display']) ?>"><?= $person['display'] ?></a></td>
<?php
  if($user['idnum'] > 0)
  {
    $age = birthdate_to_age($person['birthday']);
  
    echo '<td class="centered">' . (($age <= 13 || $person['show_age'] == 'no') ? '-' : $age) . '</td><td>';

    if($person['gender'] == 'male')
      echo '<img src="/gfx/boy.gif" height="12" width="12" alt="(male)" />';
    else if($person['gender'] == 'female')
      echo '<img src="/gfx/girl.gif" height="12" width="12" alt="(female)" />';

    echo '</td><td>' . $person['location'] . '</td>';
  }
  
  echo '</tr>';

  $rowclass = alt_row_class($rowclass);
}
?>
     </table>
     <?= $pages ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
