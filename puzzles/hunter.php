<?php
if($challenge['step'] == 0)
  exit();

echo '<p>You encounter a hunter...</p>';

if($challenge['difficulty'] == 0)
{
?>
<p>You meet a fisherman near a river...</p>
<p>"Come fishing, eh?  Hm?  No?  A shame!  They're really biting!  In fact, I'm nearly out of bait.  You wouldn't happen to have a bit of Jerky I could use?"</p>
<?php
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Jerky\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching bear traps in storage storage');

  if($data['c'] == 0)
    echo '<p><i>(You do not have any Jerky in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the fisherman a Jerky</a></li></ul>';
}
else if($challenge['difficulty'] == 1)
{
?>
<p>"Good day to you!  Are you also hunting Albino Duck?  No?  In that case, do you suppose you could lend me a Bear Trap?"</p>
<?php
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bear Trap\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching bear traps in storage storage');

  if($data['c'] == 0)
    echo '<p><i>(You do not have any Bear Traps in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the hunter a Bear Trap</a></li></ul>';
}
else if($challenge['difficulty'] == 2)
{
?>
<p>"Ah, hunting Frogs, I assume...  No?  Say, that being the case, you won't need any Nets you have, right?  Could you spare me one?  I'm having a bit of trouble catching one of these things..."</p>
<?php
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Net\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching nets in storage');

  if($data['c'] == 0)
    echo '<p><i>(You do not have any Nets in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the hunter a Net</a></li></ul>';
}
else if($challenge['difficulty'] == 3)
{
?>
<p>"Quiet, quiet!  You'll scare away the Wooly Mammoth!  That is why you're here, isn't it?  Hunting?  Ah!  It's on the move!  Quick, hand me a Compound Bow, will you?</p>
<?php
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Compound Bow\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching compound bows in storage');

  if($data['c'] == 0)
    echo '<p><i>(You do not have any Compound Bows at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the hunter a Compound Bow</a></li></ul>';
}
else if($challenge['difficulty'] == 4)
{
?>
<p>"Hunting?  You're much too noisy!  If that Dinosaur hears you, we'll both be in a bit of trouble!  Here, give me a hand, will you?  Fetch me a couple Bronze Spears and I'll take the Dinosaur down.</p>
<?php
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Bronze Spear\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching bronze spears in storage');

  if($data['c'] == 0)
    echo '<p><i>(You do not have any Bronze Spear in Storage at this time.)</i></p>';
  else if($data['c'] < 2)
    echo '<p><i>(You only have ' . $data['c'] . ' Bronze Spear in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the hunter two Bronze Spears</a></li></ul>';
}
else
  exit();
?>
