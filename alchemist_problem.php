<?php
$wiki = 'The_Alchemist\'s';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/questlib.php';

if(time() - $user['signupdate'] > 56 * 24 * 60 * 60)
{
  $questval = get_quest_value($user['idnum'], 'AlchemistQuest');
  if((int)$questval['value'] >= 2)
  {
    header('Location: ./alchemist.php');
    exit();
  }

  $inventory_items = $database->FetchMultiple("SELECT itemname FROM monster_inventory WHERE `user`=" . quote_smart($user["user"]) . " AND `location`='storage'");

  $inventory = array();

  foreach($inventory_items as $inventory_item)
    $inventory[$inventory_item['itemname']]++;

  if($inventory['Colander'] > 0 && ($inventory['Tea Kettle'] > 0 || $inventory['Bright Copper Kettle'] > 0) &&
    $inventory['Net'] > 0 && $inventory['Figurine #3'] > 0)
  {
    $acceptable = true;
  }

  if($questval['value'] == 1)
  {
    $quest_accepted = true;
    
    if($acceptable && $_GET['dialog'] == 4)
    {
      delete_inventory_byname($user['user'], 'Colander', 1, 'storage');

      if($inventory['Bright Copper Kettle'] > 0)
        delete_inventory_byname($user['user'], 'Bright Copper Kettle', 1, 'storage');
      else
        delete_inventory_byname($user['user'], 'Tea Kettle', 1, 'storage');

      delete_inventory_byname($user['user'], 'Net', 1, 'storage');
      delete_inventory_byname($user['user'], 'Figurine #3', 1, 'storage');

			if($user['idnum'] <= 40483)
				give_money($user, 200, 'Paid to you by Thaddeus the Alchemist');

			update_quest_value($questval['idnum'], 2);
      $quest_complete = true;
    }
  }
  else if($_GET['dialog'] == 3)
  {
    add_quest_value($user['idnum'], 'AlchemistQuest', 1);
    $quest_accepted = true;
  }
}

$options = array();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Alchemist's</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Alchemist's</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="alchemist.php">General Shop</a></li>
      <li><a href="alchemist_potions.php">Potion Shop</a></li>
      <li><a href="alchemist_pool.php">Cursed Pool</a></li>
      <li><a href="alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
echo '<img src="gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" />';

if($quest_complete)
{
  include 'commons/dialog_open.php';
?>
<p>Excellent!  Most excellent!  I can't thank you enough.</p>
<?php
if($user['idnum'] <= 40483)
	echo '<p>Please take 200 moneys.  I hope that covers the cost of the supplies.  But also... remember that pool I have in the back?  How about I let you make use of that once - free of charge, eh?  Ask me any time.  I won\'t forget.</p>';
?>
<p>Now please, come inside now that everything's fixed up!</p>
<?php
  include 'commons/dialog_close.php';

  $options[] = '<a href="alchemist.php">Visit The Alchemist\'s</a>';
}
else if($quest_accepted)
{
  include 'commons/dialog_open.php';
?>
<p>Thanks a lot for helping me out.  Here's a list of what I'm missing.  You can probably find a lot of it in the <a href="/fleamarket/">Flea Market</a>:</p>
<ul>
 <li><a href="encyclopedia2.php?item=Colander">Colander</a></li>
 <li><a href="encyclopedia2.php?item=Bright+Copper+Kettle">Bright Copper Kettle</a> (a regular <a href="encyclopedia2.php?item=Tea+Kettle">Tea Kettle</a> will do, but I'd prefer a <a href="encyclopedia2.php?item=Bright+Copper+Kettle">Bright Copper Kettle</a>)</li>
 <li><a href="encyclopedia2.php?item=Net">Net</a></li>
 <li><a href="encyclopedia2.php?i=50">Figurine #3</a></li>
</ul>
<?php
  include 'commons/dialog_close.php';

  if($acceptable)
    $options[] = '<a href="alchemist_problem.php?dialog=4">Give Thaddeus the items</a>';
  else
    echo '<p><i>(The items must be in your Storage in order to give them away.)</i></p>';
}
else if($_GET['dialog'] == 2)
{
  include 'commons/dialog_open.php';
?>
<p>What, Broccoli?!  Those things are nothing but trouble!  I don't know why you guys keep them as pets... <span class="size8">better off cooking them... <span class="size7">a little cheese on top... <i>*mutter, mutter*</i></span></span></p>
<?php
  include 'commons/dialog_close.php';

  $options[] = '<a href="alchemist_problem.php?dialog=3">Accept the job</a>';
}
else
{
  include 'commons/dialog_open.php';
?>
<p>Dammit!</p>
<p>Oh, <?= $user['display'] ?>!  Sorry about that... you've come at a bad time: some of those f--</p>
<p>*sigh*</p>
<p>Some of those Broccoli ran off with a bunch of my equipment while I was out.  I have no idea where they went, and--  agh!  My Figurine #3!  Ki Ri Kashu, it's broken!</p>
<p>Look, <?= $user['display'] ?>, do you think you could help me get everything set back up here?  I'd compensate you, of course.  I could really use the help...</p>
<?php
  include 'commons/dialog_close.php';

  $options[] = '<a href="alchemist_problem.php?dialog=3">Accept the job</a>';
  $options[] = '<a href="alchemist_problem.php?dialog=2">Ask how a vegetable managed to cause so much trouble</a>';
}

$options[] = '<a href="npcprofile.php?npc=' . link_safe('Thaddeus') . '">Look at Thaddeus\'s profile</a>';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';
?>
     </table>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
