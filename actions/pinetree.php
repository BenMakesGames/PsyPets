<?php
if($okay_to_be_here !== true)
  exit();

$item = $database->FetchSingle("SELECT `itemname` FROM monster_items WHERE `rare`!='yes' AND custom='no' AND recycle_for!='' AND can_pawn_for='no' AND `itemtype` NOT LIKE 'food%' ORDER BY rand() LIMIT 1");

if($item === false)
{
	echo '<p>You shake it, but nothing happens.</p><p>Hm.</p>';
}
else
{
	$database->FetchNone("UPDATE monster_inventory SET itemname='Shaken Potted Pine', message='', message2='' WHERE idnum=" . $this_inventory['idnum'] . " LIMIT 1");

	add_inventory($user['user'], '', $item["itemname"], 'Found in a Potted Pine', 'storage/incoming');
	flag_new_incoming_items($user['user']);

	$i = rand() % 10;

	if($i == 7)
		echo '<p>You shake it.  Like a baby.</p>';
	else if($i > 7)
		echo '<p>You shake it (sh-sh shake it, shake it) like a Polaraoid picture.</p>';
	else
		echo '<p>You shake it.  Kinda\' like a snow globe?</p>';

	echo '<p>Oh!  Something fell out!  (Find it in storage/incoming).</p>';
}
?>
