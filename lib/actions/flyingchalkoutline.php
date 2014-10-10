<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/questlib.php';

$ASKS = array(
	'The Sleeping Moon',
	'Wooden Bear',
	'Plumage',
	'Magnet',
	'Hydrogen',
	'The Smiling Sun',
);

$ASK_DESCRIPTIONS = array(
	'The Sleeping Moon' => 'I\'ve always wanted to see the moon up close; not filtered by a telescope, but as it truly is!  Will you show it to me?',
	'Wooden Bear' => 'I dream of seeing the fearsome creatures of the forest!  Will you show one to me?',
	'Plumage' => 'I\'ve often spied the plumage of a bird, but they fly off before I can admire them in detail!  Can you show me some?',
	'Magnet' => 'Though I do not yet understand it, the power of magnetism seems wonderful!  Almost magical!  Do you have a Magnet I could study?',
	'Hydrogen' => 'I\'ve heard people speak of the most abundant material in the universe, but what is it?  <em>Where</em> is it?  Will you show it to me?',
	'The Smiling Sun' => 'Long have I felt the warm rays of the sun on my chalky frame, but never have I seen it directly, for it hides behind an unearthly glow!  Can you show it to me?',
);

$REWARDS = array(
	'1 Character' => 1,
	'Really Enormously Tremendous Rock' => 1,
	'Poisonous Mushroom' => 1,
	'Potted Cornstalk' => 1,
	'Strawberry Margarita' => 1,
	'Tower Monkey Repellent' => 1,
	'Pie Crust' => 3,
	'Photon' => 2,
);

$quest = get_quest_value($user['idnum'], 'flying chalk outline');
if($quest === false)
{
	$value = array_rand($ASKS);

	add_quest_value($user['idnum'], 'flying chalk outline', $value);
	$quest = get_quest_value($user['idnum'], 'flying chalk outline');
}	

$value = (int)$quest['value'];

if($_GET['action'] == 'oblige')
{
	$deleted = delete_inventory_byname($user['user'], $ASKS[$value], 1, $this_inventory['location']);
	
	if($deleted > 0)
	{
		delete_inventory_byid($this_inventory['idnum']);

		$AGAIN_WITH_ANOTHER = true;
		
		$reward = array_rand($REWARDS);
		$quantity = $REWARDS[$reward];

		add_inventory_quantity($user['user'], '', $reward, 'Given to ' . $user['display'] . ' by a ' . $this_inventory['itemname'], $this_inventory['location'], $quantity);
		
		update_quest_value($quest['idnum'], array_rand($ASKS));
		
		$all_done = true;
	}
}

if($all_done)
{
	$EXCLAMATIONS = array('Ah!', 'Oh!', 'I see!', 'Wow!', '...!', '*gasp*');
	$DESCRIPTIONS = array('It\'s even more wonderful than I thought it would be!', 'I didn\'t expect it to be like this!  Amazing!', 'It truly is beautiful!', 'There really is nothing else like it!');
	$INSPIRED = array('You\'ve inspired me!',  'I\'ve decided:', 'I\'ll never forget this!', '');
	$GOODBYES = array('Goodbye!', 'Farewell!', 'Au revoir!', 'Sayonara!', 'Auf Wiedersehen!', 'Perhaps we\'ll meet again!');
	
	echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/items/' . $this_item['graphic'] . '" align="left" style="margin: 0 10px 10px 0;" />';
	echo '<p>"' . $EXCLAMATIONS[array_rand($EXCLAMATIONS)] . '  ' . $DESCRIPTIONS[array_rand($DESCRIPTIONS)] . '</p>';
	echo '<p>"Thank you, ' . $user['display'] . '!  ' . $INSPIRED[array_rand($INSPIRED)] . '  I\'m going to dedicate my life to discovering the wonders of the world!</p>';
	echo '<p>"Before I go, please accept ' . ($quantity == 1 ? 'this ' . item_text_link($reward) : 'these ' . item_text_link($reward) . 's') . ' as a token of my gratitude!</p>';
	echo '<p>"' . $GOODBYES[array_rand($GOODBYES)] . '"</p>';
	echo '<p>And with that, the ' . $this_inventory['itemname'] . ' flies away.</p>';

	require_once 'commons/statlib.php';
	record_stat($user['idnum'], 'Inspired a ' . $this_inventory['itemname'], 1);
}
else
{
	$asking_for = $ASKS[$value];
	$plea = $ASK_DESCRIPTIONS[$asking_for];
	
	echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/items/' . $this_item['graphic'] . '" align="left" style="margin: 0 10px 10px 0;" />';
	echo '<p>"' . $plea . '"</p>';
	
	$data = $database->FetchSingle('
		SELECT idnum
		FROM monster_inventory
		WHERE
			itemname=' . $database->Quote($asking_for) . '
			AND user=' . $database->Quote($user['user']) . '
			AND location=' . $database->Quote($this_inventory['location']) . '
		LIMIT 1
	');
	
	if($data !== false)
	{
		echo '<ul style="clear:both;"><li><a href="?idnum=' . $this_inventory['idnum'] . '&amp;action=oblige">Give it a ' . $asking_for . '</a></li></ul>';
	}
	else
	{
		echo '<p style="clear:both;"><i>(Whatever it is it wants, you don\'t seem to have any lying around.  At least not in this room.)</i></p>';
	}
}
?>
