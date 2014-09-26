<?php
require_once "commons/itemlib.php";

if($okay_to_be_here !== true)
  exit();

if(substr($this_inventory["location"], 0, 4) != "home")
  echo "You cannot use the " . $this_inventory["itemname"] . " away from home.";
else
{
  $quantity = (int)$_POST["quantity"];

  $myhouse = get_inventory_byuser($user["user"], $this_inventory["location"]);

  foreach($myhouse as $item)
  {
    if($item["itemname"] == "Corn")
      $corn++;
  }

  if($quantity > 0)
  {
    if($corn >= $quantity)
    {
      delete_inventory_byname($user["user"], "Corn", $quantity, $this_inventory["location"]);
      for($i = 0; $i < $quantity * 2; ++$i)
        add_inventory($user["user"], 'u:' . $user['idnum'], "Corn Syrup", "Made with the " . $this_inventory["itemname"], $this_inventory["location"]);

      $message = "You have prepared Corn Syrup!";

      $corn -= $quantity;

      $RECOUNT_INVENTORY = true;
    }
  }

  if($message)
    echo "<font style=\"color:green;\">$message</font></p>\n<p>";

  if($corn > 0)
  {
?>
You have <?= $corn ?> ear<?= $corn != 1 ? "s" : "" ?> of Corn available.</p>
<p>How much Corn Syrup would you like to prepare?</p>
<p><form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
<input name="quantity" size=2 maxlength=2 /> <input type="submit" value="Process" />
</form>
<?php
  }
  else
    echo "You do not have any Corn in this room.";
} // you're at home
?>
