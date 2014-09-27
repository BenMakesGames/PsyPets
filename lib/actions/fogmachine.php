<?php
if($okay_to_be_here !== true)
  exit();

if($_POST["action"] == "addprofile")
{
  $user["profile_wall"] = "fog.png";
  $command = "UPDATE monster_users SET profile_wall=" . quote_smart($user["profile_wall"]) . ",profile_wall_repeat='yes' WHERE idnum=" . $user["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'itemaction.php?idnum=' . $this_inventory['idnum']);
}
else if($_POST["action"] == "removeprofile")
{
  $user["profile_wall"] = "";
  $command = "UPDATE monster_users SET profile_wall=" . quote_smart($user["profile_wall"]) . " WHERE idnum=" . $user["idnum"] . " LIMIT 1";
  $database->FetchNone($command, 'itemaction.php?idnum=' . $this_inventory['idnum']);
}
?>
<form action="itemaction.php?idnum=<?= $this_inventory['idnum'] ?>" method="post">
<?php
if($user["profile_wall"] == "fog.png")
{
?>
<input type="hidden" name="action" value="removeprofile" /><input type="submit" value="Turn off Fog Machine" />
<?php
}
else
{
?>
<input type="hidden" name="action" value="addprofile" /><input type="submit" value="Turn on Fog Machine" />
<?php
}
?>
</form>
