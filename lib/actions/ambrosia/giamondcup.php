<?php
if($okay_to_be_here !== true)
  exit();

require_once "commons/petlib.php";
require_once 'commons/statlib.php';

if(count($userpets) == 0)
{
  echo '<p><i>You have no pet to give this to.</i></p>';
}
else
{
	if($_POST["petid"] > 0 && (int)$_POST["petid"] == $_POST["petid"])
		$target_pet = get_pet_byid((int)$_POST["petid"]);
	else
		$target_pet = array();

	if($target_pet['user'] != $user['user'])
	{
?>
<p>The <?= $this_inventory['itemname'] ?> appears to be filled with some kind of sparkling gas!</p>
<p>Which pet will you sprinkle it on?</p>	
	<form method="post">
	<p><select name="petid">
	<?php
		foreach($userpets as $this_pet)
		{
			echo '<option value="' . $this_pet['idnum'] . '">' . $this_pet['petname'] . '</option>';
		}
	?>
	</select>&nbsp;<input type="submit" name="submit" value="Sprinkle" /></p>
	</form>
	<?php
	}
	else
	{
		$target_pet['last_love'] = 1;
		$target_pet['love_exp']++;
		save_pet($target_pet, array('last_love', 'love_exp'));

		delete_inventory_byid($this_inventory['idnum']);
		
		record_stat($user['idnum'], 'Emptied a ' . $this_inventory['itemname'], 1);
		
		echo '<p>You turn the cup over, and once empty of sparkles, it vanishes!</p>';

		$AGAIN_WITH_ANOTHER = true;
	}
}
?>
