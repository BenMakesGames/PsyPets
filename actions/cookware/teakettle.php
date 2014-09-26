<?php
// preserved for custom items - do not use this script :P

if($okay_to_be_here !== true)
  exit();

require_once "commons/itemlib.php";

if(substr($this_inventory["location"], 0, 4) != "home")
  echo "You cannot use the tea kettle away from home.";
else
{
  $quantity = (int)$_POST["quantity"];

  $myhouse = get_inventory_byuser($user["user"], $this_inventory["location"]);

  foreach($myhouse as $item)
  {
    if($item["itemname"] == "Tea Leaves")
      $tea_leaves++;
  }

  if($quantity > 0)
  {
    if($tea_leaves >= $quantity)
    {
      delete_inventory_byname($user["user"], "Tea Leaves", $quantity, $this_inventory["location"]);
      for($i = 0; $i < $quantity; ++$i)
        add_inventory($user["user"], 'u:' . $user['idnum'], "Tea", 'Made with a ' . $this_inventory['itemname'], $this_inventory["location"]);

      if($quantity < 5)
        $descript = "Tea";
      else if($quantity < 11)
        $descript = "some Tea";
      else if($quantity < 31)
        $descript = "a lot of Tea";
      else
        $descript = "more Tea than " . $userpets[0]["petname"] . " can shake a stick at";

      $message = "You have prepared $descript!";
      
      $tea_leaves -= $quantity;
    }
  }

  if($message)
    echo "<font style=\"color:green;\">$message</font></p>\n<p>";

  if($tea_leaves > 0)
  {
?>
You have <?= $tea_leaves ?> Tea Leaves available.</p>
<p>How much tea would you like to prepare?</p>
<p><form action="itemaction.php?idnum=<?= $_GET["idnum"] ?>" method="post">
<input name="quantity" size=2 maxlength=2 /> <input type="submit" value="Brew" />
</form>
<?php
  }
  else
    echo "You do not have any Tea Leaves.";
} // you're at home
?>
