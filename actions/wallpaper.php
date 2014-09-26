<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/houselib.php';

$house = get_house_byuser($user['idnum']);

if($house === false)
{
  echo "Failed to load your house.<br />\n";
  exit();
}

if(substr($this_inventory['location'], 0, 4) != 'home')
{
  if($this_inventory['itemname'] == 'White Paint')
    echo 'You can\'t paint this room.';
  else
    echo 'You can\'t apply ' . $this_inventory['itemname'] . ' to this room.';
}
else if($_GET['step'] == 2)
{
  if($house['curroom'] == '')
  {
    $offset = 0;
    $curroom = 'Common';
  }
  else
  {
    $wall_rooms = explode(',', $house['rooms']);
    $offset = array_search($house['curroom'], $wall_rooms) + 1;
    $curroom = $house['curroom'];
  }

  $rooms = take_apart(',', $house['rooms']);
  $walls = take_apart(',', $house['wallpapers']);

  // the first item is for the common room
  while(count($walls) < count($rooms) + 1)
    $walls[] = "none";

  $image = $action_info[2];

  $walls[$offset] = $image;

  $q_newwalls = quote_smart(implode(',', $walls));

  $database->FetchNone("UPDATE monster_houses SET wallpapers=$q_newwalls WHERE idnum=" . $house["idnum"] . " LIMIT 1");

  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>';

  if($this_inventory["itemname"] == "White Paint")
    echo '<i>You repaint the ' . ($curroom{0} == '$' ? substr($curroom, 1) : $curroom) . ' Room white.</i>';
  else
    echo '<i>The ' . $this_inventory['itemname'] . ' has been applied to the ' . ($curroom{0} == '$' ? substr($curroom, 1) : $curroom) . ' Room.</i>';

  echo '</p>';
}
else
{
  echo '
    <p>Using the ' . $this_inventory['itemname'] . ' will change the background wallpaper of the current room.</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&step=2">Good-good: that\'s what I want.</a></li></ul>
  ';
}
?>
