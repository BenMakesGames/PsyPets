<?php
if($okay_to_be_here !== true)
  exit();

delete_inventory_byid($this_inventory['idnum']);

$rupees = mt_rand(13, 17);

if(mt_rand(1, 9) == 1)
  $rupees += mt_rand(5, 10);

$command = 'UPDATE monster_users SET rupees=rupees+' . $rupees . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
$database->FetchNone($command, 'updating rupee count');
$user['rupees'] += $rupees;

echo '<p>Opening the bag reveals ' . $rupees . ' Rupees!</p>';

require_once 'commons/statlib.php';
record_stat($user['idnum'], 'Bag of Rupees Opened', 1);

$AGAIN_WITH_ANOTHER = true;
?>
