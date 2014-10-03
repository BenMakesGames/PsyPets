<?php
require_once 'commons/init.php';

$url = '/myhouse/managerooms.php';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/questlib.php';

$rooms_tutorial_quest = get_quest_value($user['idnum'], 'tutorial: rooms');
if($rooms_tutorial_quest === false)
  $no_tip = true;

$THIS_ROOM = 'Add/Remove';

$rooms = take_apart(',', $house['rooms']);
$walls = take_apart(',', $house['wallpapers']);

// the first wallpaper is for the common room
while(count($walls) < count($rooms) + 1)
  $walls[] = 'none';

$nopetrooms = take_apart(',', $house['nopet_rooms']);

if($_GET['action'] == 'delete')
{
  $room = $_GET['room'];
  $i = array_search($room, $rooms);

  if($i !== false)
  {
    unset($rooms[$i]);
    unset($walls[$i + 1]);
    
    $q_newrooms = quote_smart(implode(',', $rooms));
    $q_newwalls = quote_smart(implode(',', $walls));

    $command = "UPDATE monster_houses SET rooms=$q_newrooms,wallpapers=$q_newwalls WHERE idnum=" . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating rooms');

    $command = "UPDATE monster_inventory SET location='home' WHERE user=" . quote_smart($user['user']) . " AND location='home/$room'";
    $database->FetchNone($command, 'moving inventory to common');
    
    $_POST['success'] = 'Room deleted.  Your items there (if any) have been moved to Commons.';
  }
}
else if($_POST['action'] == 'create')
{
  $room = trim($_POST['room']);

  $duplicate_name = false;

  foreach($rooms as $this_room)
  {
    if($this_room == $room || $this_room == '$' . $room)
    {
      $duplicate_name = true;
      break;
    }
  }

  $disallowed_room_names = array(
    'storage', 'common', 'basement', 'incoming', 'tower', 'fireplace', 'moat', 'dungeon', 'menagerie', 'smokehouse',
    'windmill', 'library add-on',
  );

  if($duplicate_name)
    $_POST['message'] = 'You already have a room by that name.';
  else if(preg_match('/[^a-zA-Z0-9 ]/', $room))
    $_POST['message'] = 'Room names may only contain letters, numbers, and spaces.';
  else if(strlen($room) > 10)
    $_POST['message'] = 'Room names may not be longer than 10 characters.';
  else if(strlen($room) < 2)
    $_POST['message'] = 'Room names must be at least 2 characters long.';
  else if(in_array(strtolower($room), $disallowed_room_names))
    $_POST['message'] = 'Unfortunately, a room cannot be named "' . $room . '".  This name is reserved for special game purposes.';
  else
  {
    if($_POST['protected'] == 'yes' || $_POST['protected'] == 'on')
      $room = '$' . $room;

    if(!($_POST['showpets'] == 'yes' || $_POST['showpets'] == 'on'))
    {
      $nopetrooms[] = $room;
      $extra_update = ',nopet_rooms=' . quote_smart(implode(',', $nopetrooms));
    }

    $rooms[] = $room;
    $walls[] = 'none';

    $q_newrooms = quote_smart(implode(',', $rooms));
    $q_newwalls = quote_smart(implode(',', $walls));

    $command = 'UPDATE monster_houses SET rooms=' . $q_newrooms . ',wallpapers=' . $q_newwalls . $extra_update . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'adding new room to house');

    $_POST['success'] = 'Room created.';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Manage Rooms</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($rooms_tutorial_quest === false)
{
  include 'commons/tutorial/rooms.php';
  add_quest_value($user['idnum'], 'tutorial: rooms', 1);
}
?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Manage Rooms</h4>
<ul class="tabbed">
 <li class="activetab"><a href="/myhouse/managerooms.php">Add/Remove Rooms</a></li>
 <li><a href="/myhouse/arrangerooms.php">Arrange Rooms</a></li>
 <li><a href="/myhouse/arrangeaddons.php">Arrange Add-ons</a></li>
</ul>
     <p>Locked rooms are not accessible by pets (the items in a locked room will not be eaten, used in crafts, etc).</p>
     <p>If you delete a room which contains items, those items will be moved into the Common room.</p>
<?php
 if($_POST['message'])
   echo '<p class="failure">' . $_POST['message'] . '</p>';
 if($_POST['success'])
   echo '<p class="success">' . $_POST['success'] . '</p>';
?>
     <table>
      <tr class="titlerow">
       <th class="centered"><img src="/gfx/roomlock.png" width="10" height="11" alt="Locked?" title="Locked?" /></th>
       <th>Room</th>
       <th class="centered">Action</th>
      </tr>
<?php
$bgcolor = begin_row_class();

if(count($rooms) > 0)
{
  foreach($rooms as $room)
  {
    if($room{0} == '$')
      $roomname = substr($room, 1);
    else
      $roomname = $room;
?>
      <tr class="<?= $bgcolor ?>">
<?php
    if($room{0} == '$')
      echo '<td class="centered">&#10004;</td>';
    else
      echo '<td></td>';
?>
       <td><?= $roomname ?></td>
       <td class="centered"><a href="<?= $url ?>?action=delete&room=<?= $room ?>" onclick="return confirm('Really delete the <?= $roomname ?> room?')"><img src="/gfx/trash.png" alt="delete" /></a><a href="managerooms_edit.php?room=<?= $room ?>"><img src="/gfx/pencil.png" alt="edit" /></a></td>
      </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
}
?>
      <form action="<?= $url ?>" method="post">
      <input type="hidden" name="action" value="create" />
      <tr class="<?= $bgcolor ?>">
       <td><input type="checkbox" name="protected" /></td>
       <td><input name="room" maxlength="10" size="10" /></td><td><input type="submit" value="Create" /></td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
