<?php
$wiki = 'City_Hall#Room_106';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/checkpet.php';
require_once 'commons/questlib.php';

$questval = get_quest_value($user['idnum'], 'Allowance Miniquest');
$alien_quest = get_quest_value($user['idnum'], 'crop circle aliens');

if($questval['value'] == 1)
{
  $allowance_miniquest = true;

  if($_GET['dialog'] == 2)
  {
    $allowance_miniquest_dialog = true;
    update_quest_value($questval['idnum'], 2);
    add_inventory($user['user'], '', 'Welcome Pamphlet', 'This item was given to you by Claire', 'storage/incoming');
  }
}

if($EASTER)
{
  $command = 'SELECT COUNT(idnum) AS c FROM monster_inventory WHERE itemname=\'Plastic Egg\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $data = $database->FetchSingle($command, 'cityhall_106.php');
  $egg_count = $data['c'];

  $exchanges = array(
    1 => array(1, 'Marshmallow Chick'),     // 45 minutes
    3 => array(2, 'Collared Yellow Cape'),  // 1.5 hours
    2 => array(5, 'Rabbit Hat'),            // 3 hours, 45 minutes
    7 => array(10, 'Yellow Bunny Plushy'),  // 7.5 hours
    4 => array(15, 'Bunny Ear Wand'),       // 11 hours, 15 minutes
    8 => array(20, 'Copper-Dyed Egg'),      // 15 hours
    5 => array(30, 'Silver-Dyed Egg'),      // 22.5 hours
    6 => array(40, 'Gold-Dyed Egg'),        // 30 hours
  );

  if($_POST['action'] == 'trade')
  {
    $trade = (int)$_POST['item'];
  
    $details = $exchanges[$trade];
  
    if(count($details) == 2)
    {
      if($egg_count >= $details[0])
      {
        delete_inventory_fromstorage($user['user'], 'Plastic Egg', $details[0]);
        add_inventory($user['user'], '', $details[1], 'You traded with Eve Heidel for this item.', 'storage/incoming');

        $egg_count -= $details[0];

        $_GET['dialog'] = 3;
      }
      else
        $_GET['dialog'] = 4;
    }
  }
}

$quest_totem = get_quest_value($user['idnum'], 'totem quest');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; City Hall &gt; Room 106</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>City Hall &gt; Room 106</h4>
     <ul class="tabbed">
      <li><a href="cityhall.php">Bulletin Board</a></li>
      <li><a href="/help/">Help Desk</a></li>
      <li class="activetab"><a href="cityhall_106.php">Room 106</a></li>
<?php
if($quest_totem['value'] >= 4)
  echo '<li><a href="cityhall_210.php">Room 210</a></li>';
?>
      <li><a href="af_resrename2.php">Name Change Application</a></li>
      <li><a href="af_movepet2.php">Pet Exchange</a></li>
     </ul>
<?php
if($EASTER)
{
  if($_GET['dialog'] == 2)
  {
?>
     <img src="gfx/npcs/eve_heidel.png" align="right" width="350" alt="(HERG Director Eve Heidel)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>Oh, were you not here two years ago?  Sorry, let me explain...</p>
     <p>Two years ago at about this time, these pink eggs started showing up.  We had initially announced that they were Plastic Eggs that HERG had hidden for you guys, but in truth they were the eggs of a mysterious new species from the Hollow Earth.</p>
     <p>Sorry about the deceit.  I can only say that we felt it necessary at the time.</p>
     <p>Anyway, it seems that every year, at about this time, these creatures - these eared, legged fish - come through from the Hollow Earth to lay their eggs.  On Easter midnight the eggs hatch, and the babies scurry into the depths of the ocean, searching for a tunnel back home.</p>
     <p>We're very curious to learn more about these creatures, and are, consequentially, offering a reward to anyone who can bring us back these eggs before they hatch and escape.  The more the better!</p>
     <p>Oh, and don't be fooled by their appearance: these eggs are not actually plastic!  Please handle them carefully.</p>
<?php
    include 'commons/dialog_close.php';
  }
  else if($_GET['dialog'] == 3)
  {
?>
     <img src="gfx/npcs/eve_heidel.png" align="right" width="350" alt="(HERG Director Eve Heidel)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>Great work, <?= $user['display'] ?>.  I've put the item you requested in your storage.</p>
<?php
    include 'commons/dialog_close.php';
  }
  else if($_GET['dialog'] == 4)
  {
?>
     <img src="gfx/npcs/eve_heidel.png" align="right" width="350" alt="(HERG Director Eve Heidel)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>I'm sorry, but I have to insist that you have enough Eggs to make the trade.  I'm sure if you put your mind to it it won't take you too long to find more.</p>
<?php
    include 'commons/dialog_close.php';
  }
  else
  {
?>
     <img src="gfx/npcs/eve_heidel.png" align="right" width="350" alt="(HERG Director Eve Heidel)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>I'm only taking eggs for the next <?= Duration(mktime(0, 0, 0, $easter_month, $easter_day + 1, $now_year) - $now, 2) ?>.  Shortly after then they'll all hatch, and I'll need to be back in the lab by then.</p>
     <p>And don't bother keeping any of the Plastic Eggs, either.  Trust me: you won't be able to hold on to any of the little guys after the eggs hatch.  At the very least, I can give you these cute Marshmallow things.</p>
<?php include 'commons/dialog_close.php'; ?>
     <ul>
      <li><a href="cityhall_106.php?dialog=2">Ask what in the world she's talking about...</a></li>
     </ul>
<?php
  }
?>

     <p>You have <?= $egg_count ?> "Plastic" Egg<?= $egg_count != 1 ? 's' : '' ?> in your Storage.</p>
     <form action="cityhall_106.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Eggs</th>
       <th></th>
       <th></th>
       <th>Reward</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($exchanges as $index=>$data)
  {
    $item_details = get_item_byname($data[1]);
?>
      <tr class="<?= $rowclass ?>">
       <th><input type="radio" name="item" value="<?= $index ?>"<?= $egg_count < $data[0] ? ' disabled' : '' ?> /></th>
       <th class="centered"><?= $data[0] ?></th>
       <th><img src="gfx/lookright.gif" height="16" alt="16" alt="" /></th>
       <th class="centered"><?= item_display($item_details, '') ?></th>
       <th><?= $item_details['itemname'] ?></th>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <p><input type="hidden" name="action" value="trade" /><input type="submit" value="Claim Reward" class="bigbutton" /></p>
     </form>
<?php
}
else if($allowance_miniquest_dialog)
{
?>
<img src="gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>Oh!  You found me just to ask about that?</p>
     <p>Well, no it isn't a government grant.  When you mention <em>the Hollow Earth</em>, well, people tend to stop listening, I guess you'd say.</p>
     <p>No, we're funded by a private business called telkoth.net.  I'm not really sure what they do, but it seems that they're very interested in the Hollow Earth, enough so to have purchased this entire island for our use, the tidal generators to power it, and, yes, your "allowances."</p>
     <p>We have some pamphlets about it... oh, in fact, I have one right here.</p>
     <p>Go ahead and keep it.  I'm surprised you didn't get one when you first signed up to participate.</p>
<?php include 'commons/dialog_close.php'; ?>
     <p><i>(A Welcome Pamphlet has been placed into your storage.)</i></p>
<?php
}
else if($allowance_miniquest)
{
?>
<img src="gfx/npcs/receptionist.png" align="right" width="350" height="275" alt="(Claire the City Hall receptionist)" />
<?php include 'commons/dialog_open.php'; ?>
     <p>Oh, hi.  <?= $user['display'] ?>, right?</p>
     <p>Sorry, did you have this room reserved?  I was just trying to get some work done here - it's a little busy in the office - but if I'm in your way, I can find another room.</p>
<?php include 'commons/dialog_close.php'; ?>
<ul><li><a href="cityhall_106.php?dialog=2">Ask where the money to pay for allowance comes from...</a></li></ul>
<?php
}
else
  echo '     <p><i>The room is largely empty, save for a few chairs, tables, a white board and a computer terminal.</p><p>There\'s a door in the far corner of the room, but the window is covered from the inside with what is probably a black plastic trash bag.</i></p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
