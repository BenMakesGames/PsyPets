<?php
if($okay_to_be_here !== true)
  exit();

$data = $this_inventory['data'];

if($data == 'used')
{
  echo '<p>The ring refuses to speak a second time.  You try to convince it with compliments, even going so far as to refer to it as "precious", but all to no avail.</p>';
}
else
{
  $badges = get_badges_byuserid($user['idnum']);

  $command = 'UPDATE monster_inventory SET data=\'used\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'setting ring as used');

  $items = array(
    'Gold Ring',              // 30%
    'Gold Ring',
    'Gold Ring',
    'Gold Ring',
    'Gold Ring',
    'Gold Ring',
    'Silver Ring',            // 20%
    'Silver Ring',
    'Silver Ring',
    'Silver Ring',
    'Silver Ring',
    'Onion Rings',            // 10%
    'Onion Rings',
    'Key Ring',               // 10%
    'Key Ring',
    'Cheap Plastic Earrings', // 5%
    'Pyrestone Earrings',     // 5%
    'Soldering Iron',         // 5%
    'Spring Season',          // 5%
    'Lucky Ring',             // 5%
    'Preparing Steak',        // 5%
  );
  
  if(mt_rand(1, 200) == 1)
    $itemname = 'Ring of Regeneration +1/3';
  else
    $itemname = $items[array_rand($items)];

  echo '
    <p>You hear the muttering of some ancient language coming from the ring itself!</p>
    <p style="margin-left:50px;"><img src="//saffron.psypets.net/gfx/ancientscript/oneringdialog.png" /></p>
    <p>When it\'s finished, a ' . $itemname . ' falls to your feet out of nowhere.</p>
  ';

  if($badges['ringbearer'] == 'no')
  {
    set_badge($user['idnum'], 'ringbearer');
    echo '<p><i>(You received the Ring-Bearer Badge!)</i></p>';
  }

  add_inventory($user['user'], '', $itemname, '', $this_inventory['location']);
}
?>
