<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/userlib.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/globals.php';
require_once 'commons/threadfunc.php';
require_once 'commons/itemlib.php';

$_GET['idnum'] = (int)$_GET['idnum'];

$this_inventory = get_inventory_byid($_GET['idnum']);

if($this_inventory === false)
{
  header('Location: /myhouse.php');
  exit();
}

if($this_inventory['user'] != $user['user'])
{
  header('Location: /myhouse.php');
  exit();
}

$this_item = get_item_byname($this_inventory['itemname']);

if($this_item === false)
{
  header('Location: /myhouse.php');
  exit();
}

if(strlen($this_item['action']) == 0)
{
  header('Location: /myhouse.php');
  exit();
}

$action_info = explode(';', $this_item['action']);

$location = $this_inventory['location'];

if($location == 'storage/incoming')
{
  $at_home = false;
  $raw_loc = $user['display'] . '\'s Incoming';
  $this_loc = '<a href="/incoming.php">' . $raw_loc . '</a>';
}
else if($location == 'storage/locked')
{
  $at_home = false;
  $raw_loc = $user['display'] . '\'s Locked Storage';
  $this_loc = '<a href="/storage_locked.php">' . $raw_loc . '</a>';
}
else if($location == 'storage')
{
  $at_home = false;
  $raw_loc = $user['display'] . '\'s Storage';
  $this_loc = '<a href="/storage.php">' . $raw_loc . '</a>';
}
else if($location == 'home')
{
  $at_home = true;
  $raw_loc = $user['display'] . '\'s House';
  $this_loc = '<a href="/houseaction.php?room=Common">' . $raw_loc . '</a>';
}
else if($location == 'storage/mystore')
{
  $at_home = false;
  $raw_loc = $user['display'] . '\'s Store';
  $this_loc = '<a href="/mystore.php">' . $raw_loc . '</a>';
}
else if($location == 'storage/outgoing')
{
  header('Location: /outgoing.php');
  exit();
}
else if($location == 'pet')
{
  header('Location: /myhouse.php');
  exit();
}
else
{
  $at_home = true;
  $room = substr($location, 5);

  if($room{0} == '$')
    $room_display = substr($room, 1);
  else
    $room_display = $room;

  $raw_loc = $user['display'] . "'s House &gt; $room_display Room";
  $this_loc = '<a href="houseaction.php?room=Common">' . $user['display'] . '\'s House</a> &gt; <a href="myhouse.php">' . $room_display . ' Room</a>';
}

if($action_info[1] == 'pinetree.php')
  $FORCED_GIFT = true;
else if($action_info[1] == 'shutup.php')
  $FORCE_UNIVERSE_DISPLAY = true;
else if($action_info[1] == 'backgroundchanger.php')
{
  if($_GET['ala'] == 'kazam')
  {
    $command = 'UPDATE monster_users SET style_background=' . quote_smart($action_info[2]) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating background');
    
    $user['style_background'] = $action_info[2];
  }
}

$okay_to_be_here = true;

$AGAIN_WITH_ANOTHER = false;
$AGAIN_WITH_SAME = false;
$RECOUNT_INVENTORY = false;

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $raw_loc . ' &gt; ' . $this_item['itemname'] . ' &gt; ' . $action_info[0] ?></title>
<?php include 'commons/head.php'; ?>
  <style type="text/css">
   .booktext p
   {
     text-indent: 2em;
   }
  </style>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
echo '<h4>', $this_loc, ' &gt; ', $this_item['itemname'], ' &gt; ', $action_info[0], '</h4>';

$file = 'actions/' . $action_info[1];

if(file_exists($file))
  require 'actions/' . $action_info[1];
else
  echo '<p>Error loading item action.  Please notify <a href="admincontact.php">an administrator</a>.</p>';

echo '<div style="border-top: 1px dashed #ccc; padding-bottom:14px;"></div><ul>';

if($AGAIN_WITH_SAME)
  echo '      <li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '">Do it again!</a></li>';

if($AGAIN_WITH_ANOTHER)
{
  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=' . quote_smart($this_inventory["itemname"]) . " AND location=" . quote_smart($this_inventory['location']) . ' AND idnum!=' . $this_inventory['idnum'] . ' LIMIT 1';
  $other_item = $database->FetchSingle($command, 'fetching other item');

  if($other_item !== false)
    echo '      <li><a href="itemaction.php?idnum=' . $other_item['idnum'] . '">Do this again with another ' . $this_item['itemname'] . '</a></li>';
}

echo '<li>Back to ', $this_loc, '</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
<?php
if($RECOUNT_INVENTORY && $at_home)
{
  require_once 'commons/houselib.php';

	$house = get_house_byuser($user['idnum']);

	if($house === false)
	{
		echo "Failed to load your house.<br />\n";
		exit();
	}

  recount_house_bulk($user, $house);
}
?>
