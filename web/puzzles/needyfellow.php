<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching chalk in storage');
?>
<p>An apprentice wizard sneaks up on you...</p>
<p>"Ah!  A traveller!  Say, um, I'm embarrassed to say that I'm in a bit of bind... my master told me to fetch him some Chalk for a summoning circle, and I don't have the moneys for it... I don't suppose you could spare a bit of Chalk?"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Chalk in your Storage.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the apprentice Chalk</a></li></ul>';
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Pinecone\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching pinecones in storage');
?>
<p>A lone squirrel besets you...</p>
<p><img src="/gfx/monsters/squirrel.png" align="left" height="48" width="48" /> "I could really use your help, if it's not too much trouble," the squirrel says.  "I'm looking for Pinecones.  Could you spare a couple?"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Pinecones in Storage at this time.)</i></p>';
  else if($data['c'] == 1)
    echo '<p><i>(You only have ' . $data['c'] . ' Pinecone in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the squirrel two Pinecones</a></li></ul>';
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Chalk\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching chalk in storage');
?>
<p>A wizard stops you in your path!</p>
<p>"Stop, friend!  A most unsettling circumstance is upon us!  A terrible spirit roams this area, sowing discord and... disharmony!  Many and sundry things!  Um..."  He seems more than a little frantic.  "At any rate, I think together we can banish this thing, or at least seal it away!  Do you have any Chalk on you?  I think six pieces would do the trick for drawing the appropriate seal!"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Chalk in Storage at this time.)</i></p>';
  else if($data['c'] < 6)
    echo '<p><i>(You only have ' . $data['c'] . ' Chalk in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the wizard six Chalk</a></li></ul>';
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gossamer\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gossamer in storage');
?>
<p>You encounter an angel who is in a pitiable state...</p>
<p>"Oh, thank Ki Ri Kashu I've found someone!"  She's nearly crying.  "I tore my wing flying in here, and can't get myself airborne again no matter how I try.  If you have a piece of Gossamer I could use to repair it, I would be forever indebted to you!"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Gossamer in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the angel Gossamer</a></li></ul>';
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Dark Gossamer\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching dark gossamer in storage');
?>
<p>You encounter a very upset-looking angel...</p>
<p>"Ah!  Finally!  I am in a very terrible spot.  Would you look at this?" she gestures to a wing, "completely torn up!  Ripped to pieces!  One of those damn spider eagles dove at me - can you believe those things?  Well I'm afraid I don't have anything to give you in return, but do you think you could give me some Dark Gossamer to patch this wing up?  I really wouldn't like to stay here any longer than possible."</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Dark Gossamer in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the angel Dark Gossamer</a></li></ul>';
}
else
  exit();
?>
