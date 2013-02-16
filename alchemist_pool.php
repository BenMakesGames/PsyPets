<?php
$wiki = 'The_Alchemist\'s';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/checkpet.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/alchemylib.php';
require_once 'commons/questlib.php';

$free_use = false;

if(time() - $user['signupdate'] > 56 * 24 * 60 * 60)
{
  $questval = get_quest_value($user['idnum'], 'AlchemistQuest');
  if((int)$questval['value'] < 2)
  {
    header('Location: ./alchemist_problem.php');
    exit();
  }

  if($questval['value'] == 2 && $user['idnum'] <= 40483)
    $free_use = true;
}

$inventory = $database->FetchMultipleBy('
	SELECT COUNT(idnum) AS qty,itemname
	FROM monster_inventory
	WHERE
		`user`=' . $database->Quote($user["user"]) . '
		AND `location`=\'storage\'
	GROUP BY itemname
', 'itemname');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Alchemist's &gt; Cursed Pool</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4>The Alchemist's &gt; Cursed Pool</h4>
     <ul class="tabbed">
      <li><a href="/alchemist.php">General Shop</a></li>
      <li><a href="/alchemist_potions.php">Potion Shop</a></li>
      <li class="activetab"><a href="/alchemist_pool.php">Cursed Pool</a></li>
      <li><a href="/alchemist_transmute.php">Pet Transmutations</a></li>
     </ul>
<?php
if(strlen($_GET["msg"]) > 0)
  $error_message = form_message(explode(",", $_GET["msg"]));

echo '<a href="/npcprofile.php?npc=Thaddeus"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/thaddeus.png" align="right" width="350" height="250" alt="(Thaddeus the Alchemist)" /></a>';

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
{
?>
     <p>This is no swimming pool, mind you!  Those who step in to this pool will find their gender inverted!</p>
     <p>Not all cursed pools switch sex, of course.  In fact, these kinds of pools are a much rarer case.  Species is much more of a malleable property, and easy to perform even outside of cursed pools.  You just--</p>
     <p>Right, sorry, anyway: this pool performs the much rarer gender change, something of value to you, I understand.  I don't need to tell you that it comes at a high price, but let me know if you're interested and we can have one of your pets take a dip.</p>
<?php
  if($free_use)
    echo '<p>*ahem* not that I\'ve forgotten our agreement, of course.  I will allow you a single use of the pool free of charge.</p>';
?>
     <p>Oh, and if you have any pregnant pets, be careful about having them use this pool.  A male, after all, cannot bear children.</p>
<?php
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($free_use)
	echo '<h5>Free Use</h5>';
else
{
	echo '
		<h5>Pay Items to Use</h5>
		<p>You may pay 60 ' . item_text_link('Astrolabe') . 's, 30 ' . item_text_link('The Smiling Sun') . 's and 30 ' . item_text_link('The Sleeping Moon') . 's to allow one pet access to the Cursed Pool.</p>
	';
}

if(($inventory['Astrolabe']['qty'] >= 60 && $inventory['The Smiling Sun']['qty'] >= 30 && $inventory['The Sleeping Moon']['qty'] >= 30) || $free_use)
{
?>
  <form action="/cursedpool.php" method="post">
	<input type="hidden" name="payment" value="items" />
  <p><select name="petid">
<?php
  foreach($userpets as $pet)
    echo '   <option value="' . $pet['idnum'] . '">' . $pet['petname'] . '</a>';
?>
  </select> <input type="submit" value='"Take a dip"' /></p>
  </form>
<?php
}
else
	echo '<p class="failure">You don\'t have the items needed in your Storage.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
