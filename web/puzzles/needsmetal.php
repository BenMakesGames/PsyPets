<?php
if($challenge['step'] == 0)
  exit();

if($challenge['difficulty'] == 0)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching small giamonds in storage');
?>
<p>During the course of your travels you find yourself blocked by an electronically-operated door.  A man is hunkered down near what appears to be a (forcefully) opened access panel.</p>
<p>As you approach, he turns around, startled.</p>
<p>"Oh, phew, I thought maybe you were the police.  I'd have a bit of explaining to do, tearing open this control panel!  But then again, that might have been for the better: the nearest police station might be just on the other side of this door, in which case I'd finally be able to get past.</p>
<p>"I guess that's not likely though, or someone would have come through here by now.  No, this path probably hasn't been used for years.  Or this door.</p>
<p>"Hey, but lucky for me you came!  Maybe between the two of us we can get past this thing!</p>
<p>"'See, I've been sitting here for about an hour following blue wires, red wires, green wires... bridge rectifiers, capacitors... anyway, I think I've got it figured out.  You see this spot, right here?"</p>
<p>He points to a particular piece of metal on the wall.</p>
<p>"I'm pretty sure a control unit is behind here.  If I hit it hard enough, I might knock it loose, and get the door open.  But I'd need something heavy... like a Small Rock, or something..."</p>
<?php
  if($data['c'] < 1)
    echo '<p><i>(You do not have a Small Rock in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give him a Small Rock</a></li></ul>';
}
else if($challenge['difficulty'] == 1)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching gold in storage');
?>
<p>During the course of your travels you find yourself blocked by an electronically-operated door.  A man is hunkered down near what appears to be a (forcefully) opened access panel.</p>
<p>As you approach, he turns around, startled.</p>
<p>"Oh geezes, you scared the livin' daylights out of me!  I thought you was a copper!  ... You ain't a copper, are ya'?"
<p>You explain that you are not.</p>
<p>"Well then that's a relief, ain't it?  'Cept I got some bad news for ya': this door's sealed shut, and ain't no one gettin' through, not 'less they can bypass this here lockin' circuitry!</p>
<p>"Now I've been pokin' at this thang for the last - heck, I dunno - hour'r so, and I got it all figgured out down the last Schottky Barrier Diode.  There's just oooone problem: to override it, I'm gonna' need a good, pure conductor.  I reckon nothin' short than a piece of Gold'll do it."</p>
<?php
  if($data['c'] < 1)
    echo '<p><i>(You do not have any Gold in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the man one Gold</a></li></ul>';
}
else if($challenge['difficulty'] == 2)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Gold\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching celery in storage');
?>
<p>During the course of your travels you find yourself blocked by an electronically-operated door.  A man is hunkered down near what appears to be a (forcefully) opened access panel.</p>
<p>As you approach, he turns around, startled.</p>
<p>And then you're startled, as you realize the man is not a man at all...</p>
<p style="font-family:monospace;">"Identify yourself!"</p>
<p>You do so.</p>
<p style="font-family:monospace;">"Processing! ...</p>
<p style="font-family:monospace;">"<?= $user['display'] ?> is not a known authority figure!  You will assist me!</p>
<p style="font-family:monospace;">"This door is unresponsive, however analysis of the circuitry reveals a weakness in its design!  I require 3 Gold to reprogram the circuitry!
<p style="font-family:monospace;">"You will assist me!"</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Gold in Storage at this time.)</i></p>';
  else if($data['c'] < 3)
    echo '<p><i>(You only have ' . $data['c'] . ' Gold in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give it 3 Gold</a></li></ul>';
}
else if($challenge['difficulty'] == 3)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Argrum Steel\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching argrum steel in storage');
?>
<p>During the course of your travels you find yourself blocked by an electronically-operated door.  A man is hunkered down near what appears to be a (forcefully) opened access panel.</p>
<p>As you approach, he turns around, startled.</p>
<p>"Oh!  Oh, I thought you were the police!  Had me startled!  Haha!  But you're not!  Whew!  Close one, ya?  Haha!</p>
<p>"Ah, but now you are also trapped.  Just like me.  This door is no good.  Broken.  Won't accept my access card.</p>
<p>"I've sat here... one hour?  One hour.  Ya.  Examining these circuits.  Very complex!  But I've mapped them out.  In my mind.</p>
<p>"And look here: these two leads."</p>
<p>He points.  You look.  It doesn't make much sense.</p>
<p>"If we short the circuit, the door opens!  No doubt.  It will work.  Only one problem: the resistance must be very specific.  Ya.  If it's off just a little, this over here will trip."</p>
<p>He points again.</p>
<p>"If that happens: BAM!  Door deadlocks.  Authorities notified.</p>
<p>"There's only one metal that will work.  Only one metal has the correct resistance.  Argrum Steel.</p>
<?php
  if($data['c'] == 0)
    echo '<p><i>(You do not have any Argrum Steel in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the man one Argrum Steel</a></li></ul>';
}
else if($challenge['difficulty'] == 4)
{
  $command = 'SELECT COUNT(*) AS c FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND itemname=\'Small Rock\' AND location=\'storage\'';
  $data = fetch_single($command, 'fetching small rocks in storage');
?>
<p>During the course of your travels you find yourself blocked by an electronically-operated door.  A man is hunkered down near what appears to be a (forcefully) opened access panel.</p>
<p>As you approach, he turns around, startled.</p>
<p>"Oh, I can explain, I just--  hold on a tic: you're not the police, are you?</p>
<p>"Well then - haha - nevermind all that!  Maybe you and I can help each other out, eh?  You see this door here?"</p>
<p>You inform him that you do.</p>
<p>"Right, of course.  It's right there after all, how could you miss it.  Problem is, it's rubbish!  The access panel was damaged long before I got here, completely corroded along with the rest of the circuitry.</p>
<p>"Not that that's going to stop us, is it, mate?  I've been examining this door for about an hour, and the locking mechanism is simple.  Foolishly simple.  If hit this part here with a Small Rock - pop - it opens up easy as you like.</p>
<p>"What do you say?"</p>
<?php
  if($data['c'] < 0)
    echo '<p><i>(You do not have any Small Rocks in Storage at this time.)</i></p>';
  else
    echo '<ul><li><a href="?action=go">Give the man one Small Rock</a></li></ul>';
}
else
  exit();
?>
