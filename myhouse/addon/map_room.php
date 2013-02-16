<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Map Room';
$require_petload = 'no';

$THIS_ROOM = 'Map Room';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/maproomlib.php';
require_once 'commons/utility.php';

if(!addon_exists($house, 'Map Room'))
{
  header('Location: /myhouse.php');
  exit();
}

$first_visit = false;

$maproom = get_maproom_byuser($user['idnum'], $user['locid']);
if($maproom === false)
{
  create_maproom($user['idnum'], $user['locid']);
  $maproom = get_maproom_byuser($user['idnum'], $user['locid']);
  if($maproom === false)
  {
    echo "Failed to load your map room.  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.<br />\n";
    exit();
  }

  $first_visit = true;
}

$locations = take_apart(',', $maproom['locations']);

$fix = false;

while(in_array('', $locations))
{
  $i = array_search('', $locations);
  unset($locations[$i]);
  $fix = true;
}

if($fix)
{
  $maproom['locations'] = implode(',', $locations);
  $command = 'UPDATE psypets_maprooms SET locations=\'' . $maproom['locations'] . '\' WHERE userid=' . $user['idnum'] . ' LIMIT 1';
  fetch_none($command, 'repairing map room');
  $message_list[] = 'Your map room has been automatically repaired!  (If this message pops up for you repeatedly - or even every day or so - please let ' . $SETTINGS['author_resident_name'] . ' know!)';
}

if($_GET['sortby'] == 'levela')
{
  $maproom['sortby'] = 'level ASC';
  $command = 'UPDATE psypets_maprooms SET sortby=' . quote_smart($maproom['sortby']) . ' WHERE userid=' . $maproom['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing map room sort-order');
}
else if($_GET['sortby'] == 'leveld')
{
  $maproom['sortby'] = 'level DESC';
  $command = 'UPDATE psypets_maprooms SET sortby=' . quote_smart($maproom['sortby']) . ' WHERE userid=' . $maproom['userid'] . ' LIMIT 1';
  fetch_none($command, 'changing map room sort-order');
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Map Room</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Map Room</h4>
<?php
echo $message;

room_display($house);

if(count($locations) == 0)
{
  echo '     <p>The map room is empty.</p>';
}
else
{
  $command = 'SELECT COUNT(idnum) AS c FROM psypets_locations';
  $data = fetch_single($command, 'fetching location count');

  $rowclass = begin_row_class();

  if(count($locations) >= 10)
    echo '<div style="background-image:url(//' . $SETTINGS['static_domain'] . '/gfx/ancientscript/maproom.png); background-repeat:no-repeat; background-position:bottom right;">';
  else
    echo '<div>';
?>
     <h5>Map (<?= count($locations) . '/' . $data['c'] ?>)</h5>
     <table>
      <tr class="titlerow">
<!--       <th></th> -->
       <th>Location</th>
       <th>Type</th>
<?php
  if($maproom['sortby'] == 'level DESC')
    echo '       <th>Distance&nbsp;<a href="/myhouse/addon/map_room.php?sortby=levela" alt="Sort ascending" class="activesort">&#9650;</a></th>';
  else if($maproom['sortby'] == 'level ASC')
    echo '       <th>Distance&nbsp;<a href="/myhouse/addon/map_room.php?sortby=leveld" alt="Sort descending" class="activesort">&#9660;</a></th>';
?>
      </tr>
<?php
	$locations = $database->FetchMultiple('SELECT * FROM psypets_locations WHERE idnum IN (' . $maproom['locations'] . ') ORDER BY ' . $maproom['sortby']);

  foreach($locations as $location)
  {
/*
    if(strlen($location['graphic']) > 0)
      $graphic = '<img src="/gfx/locations/' . $location['graphic'] . '" alt="" />';
    else
      $graphic = '';
*/
?>
      <tr class="<?= $rowclass ?>">
<!--       <td class="centered"><?= $graphic ?></td> -->
       <td><?= $location['name'] ?></td>
       <td><?php
    if($location['type'] == 'gather')
      echo '';
    else if($location['type'] == 'mine')
      echo 'Mine';
    else if($location['type'] == 'lumberjack')
      echo 'Forest';
?></td>
       <td class="centered"><?= $location['level'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
   </div>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
