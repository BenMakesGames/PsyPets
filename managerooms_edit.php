<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';

$THIS_ROOM = 'Add/Remove';

$locid = $user['locid'];

$house = get_house_byuser($user['idnum'], $locid);

$rooms = take_apart(',', $house['rooms']);

$my_room = $_GET['room'];

if(!in_array($my_room, $rooms))
{
  header('Location: ./managerooms.php');
	exit();
}

$nopetrooms = take_apart(',', $house['nopet_rooms']);
$show_pets = (!in_array($my_room, $nopetrooms));

if($my_room{0} == '$')
{
  $room_display = substr($my_room, 1);
  $protected = true;
}
else
{
  $room_display = $my_room;
  $protected = false;
}

if($_POST['action'] == 'edit')
{
  $room = trim($_POST['room']);

  $duplicate_name = false;

  if($room != $my_room && '$' . $room != $my_room)
  {
    foreach($rooms as $this_room)
    {
      if($this_room == $room || $this_room == '$' . $room)
      {
        $duplicate_name = true;
        break;
      }
    }
  }

  $disallowed_room_names = array(
    'storage', 'common', 'basement', 'incoming', 'tower', 'fireplace', 'moat', 'dungeon', 'menagerie', 'smokehouse',
    'windmill', 'greenhouse',
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
    $_POST['message'] = 'Unfortunately, a room cannot be named "' . $room . '".  This name is used for special game purposes.';
  else
  {
    if($_POST['protected'] == 'yes' || $_POST['protected'] == 'on')
      $room = '$' . $room;

    // if the room is NOT a 'no pet' room...
    if($show_pets)
    {
      // ... and we want it to be a 'no pet' room:
      if(!($_POST['showpets'] == 'yes' || $_POST['showpets'] == 'on'))
      {
        $nopetrooms[] = $room;
        $extra_update = ',nopet_rooms=' . quote_smart(implode(',', $nopetrooms));
      }
    }
    // if the room is a 'no pet' room
    else
    {
      // ... and we DON'T want it to be a 'no pet' room:
      if($_POST['showpets'] == 'yes' || $_POST['showpets'] == 'on')
      {
        $i = array_search($my_room, $nopetrooms);
        if($i !== false)
          unset($nopetrooms[$i]);

        $i = array_search($room, $nopetrooms);
        if($i !== false)
          unset($nopetrooms[$i]);

        $extra_update = ',nopet_rooms=' . quote_smart(implode(',', $nopetrooms));
      }
    }

    $i = array_search($my_room, $rooms);
    $rooms[$i] = $room;

    $q_newrooms = quote_smart(implode(',', $rooms));

    $command = 'UPDATE monster_houses SET rooms=' . $q_newrooms . $extra_update . ' WHERE idnum=' . $house['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'adding new room to house');

    if($room != $my_room)
    {
      $command = "UPDATE monster_inventory SET location='home/$room' WHERE user=" . quote_smart($user['user']) . " AND location='home/$my_room'";
      $database->FetchNone($command, 'moving inventory to common');
      
      $command = "UPDATE psypets_autosort SET room='home/$room' WHERE userid=" . quote_smart($user['idnum']) . " AND room='home/$my_room'";
      $database->FetchNone($command, 'updating autosorter rules');
    }
    
    header('Location: ./managerooms.php');
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Manage Rooms &gt; <?= $my_room ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="myhouse.php"><?= $user["display"] ?>'s House</a> &gt; Manage Rooms &gt; <?= $room_display ?></h4>
     <p>Depending on the number of items in the room, and the changes being made, this operation could take a while!  Please be patient!</p>
<?php
if($_POST['message'])
  echo '<p class="failure">' . $_POST['message'] . '</p>';
?>
     <form action="managerooms_edit.php?room=<?= $my_room ?>" method="post">
     <table>
      <tr><th>Room name:</th><td><input name="room" maxlength="10" size="10" value="<?= $room_display ?>" /></td></tr>
      <tr><th>Locked:</th><td><input type="checkbox" name="protected"<?= $protected ? ' checked="checked"' : '' ?> /></td></tr>
      <tr><th>Show pets:</th><td><input type="checkbox" name="showpets"<?= $show_pets ? ' checked="checked"' : '' ?> /></td></tr>
     </table>
     <p><input type="button" onclick="location.href='managerooms.php';" value="Cancel" /> <input type="hidden" name="action" value="edit" /><input type="submit" value="Update" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
