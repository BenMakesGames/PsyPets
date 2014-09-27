<?php
if($okay_to_be_here !== true)
  exit();

$data = $this_inventory["data"];
$now = time();
$day = 60 * 60 * 24;

$get_food = false;
$dies = false;

if(strlen($data) == 0)
{
  $data = $now + $day;

  $database->FetchNone("UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1");
}
else if($now > $data)
{
  if(rand(1, 10) == 1)
  {
    $dies = true;

    $database->FetchNone("DELETE FROM monster_inventory WHERE idnum=" . $_GET["idnum"] . " LIMIT 1");
    
    $AGAIN_WITH_ANOTHER = true;
  }
  else
  {
    $data = $now + rand($day, $day * 2);
    $get_food = true;

    $database->FetchNone("UPDATE monster_inventory SET data='$data' WHERE idnum=" . $_GET["idnum"] . " LIMIT 1");
  }
}

if($get_food)
{
  $foods = array(
    "Aging Root",
    "Mint Leaves",
    "Bonsai Tree",
    "Rubber",
    "Beet",
    "Baking Chocolate",
    "Fire Spice",
    "Potato",
    "Tomato",
    "Yeast",
  );

  $itemname = $foods[array_rand($foods)];

  add_inventory($user["user"], 'u:' . $user['idnum'], $itemname, "Picked from a " . $this_inventory['itemname'], $this_inventory["location"]);
  echo "<p>You root around in the " . $this_inventory["itemname"] . ", finally picking fresh " . $itemname . ".</p>";
}
else if($dies)
  echo "<p>The plants inside the " . $this_inventory["itemname"] . " have died, leaving a foul smell in their passing.</p>";
else
  echo "<p>None of the plants in the " . $this_inventory["itemname"] . " are yet mature enough to harvest.</p>";

if(!$dies)
{
  if($_GET['action'] == 'aquarium')
  {
    $command = 'INSERT INTO monster_projects (`type`, `userid`, `itemid`, `progress`, `notes`) ' .
               "VALUES ('construct', " . $user['idnum'] . ', 27, 0, \'You started this construction.\')';
    $database->FetchNone($command, 'starting project for house add-on');

    echo '<p>You set up the foundations for the Aquarium project.</p>';

    delete_inventory_byid($this_inventory['idnum']);

    $AGAIN_WITH_ANOTHER = true;
  }
  else
    echo '
      <p>It occurs to you that with a little work, this Terrarium could be transformed into an Aquarium!</p>
      <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=aquarium">Sounds like a plan!</a></li></ul>
    ';
}
?>
