<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/itemlib.php';

$delete_me = false;

$items = get_inventory_byuser($user['user'], $this_inventory['location']);

$windup_woof = false;
$windup_gator = false;
$potatohead = false;

foreach($items as $item)
{
  if($item['itemname'] == "Windup Woof")
    $windup_woof = true;
  else if($item['itemname'] == "Windup Gator")
    $windup_gator = true;
  else if($item['itemname'] == "Windup Potatohead")
    $potatohead = true;
}

if($_POST["action"] == "submit")
{
  if($_POST["key"] == "woof" && $windup_woof)
  {
    delete_inventory_byname($user["user"], "Windup Woof", 1, $this_inventory["location"]);
    $graphic = "tinylegs.gif";
    $delete_me = true;
  }
  else if($_POST["key"] == "gator" && $windup_gator)
  {
    delete_inventory_byname($user["user"], "Windup Gator", 1, $this_inventory["location"]);
    $graphic = "gator.png";
    $delete_me = true;
  }
  else if($_POST["key"] == "potatohead" && $potatohead)
  {
    delete_inventory_byname($user["user"], "Windup Potatohead", 1, $this_inventory["location"]);
    $graphic = "potatohead.png";
    $delete_me = true;
  }

  if($delete_me)
  {
    delete_inventory_byid($this_inventory["idnum"]);
    
    $petid = create_random_pet($user["user"]);

    $command = "UPDATE monster_pets SET graphic='$graphic',`protected`='yes' WHERE idnum=$petid LIMIT 1";
    $database->FetchNone($command, 'item: ' . $this_inventory['itemname']);

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Used a Purple Stone', 1);
  }
}

if($delete_me == false)
{
  if($windup_woof || $windup_gator || $potatohead)
  {
?>
Which inanimate toy will receive the pebble's blessing?</p>
<p>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<select name="key">
<?php
if($windup_woof)
  echo "<option value=\"woof\">Windup Woof</option>\n";
if($windup_gator)
  echo "<option value=\"gator\">Windup Gator</option>\n";
if($potatohead)
  echo "<option value=\"potatohead\">Windup Potatohead</option>\n";
?>
</select> <input type="hidden" name="action" value="submit" /><input type="submit" value="Make a Wish" />
</form>
<?php
  }
  else
  {
?>
You don't have anything to use the stone on. <i>(The Purple Stone can only be used on items in the same room.)</i>
<?php
  }
}
else
{
?>
It is done!!</p>
<p>And with that, the pebble silently evaporates.
<?php
}
?>
