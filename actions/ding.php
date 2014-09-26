<?php
if($okay_to_be_here !== true)
  exit();

$command = "UPDATE monster_inventory SET itemname='Plain Metal Block', message='', message2='' WHERE idnum=" . $this_inventory['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'reducing item to plain metal block');

add_inventory($user["user"], '', "Mushroom", "Found in a Question Mark Block", $this_inventory['location']);

echo "<p>*ding!*</p>\n" .
     "<p>The block turns a dull, metalic grey shortly after depositing a Mushroom.</p>\n";
?>
