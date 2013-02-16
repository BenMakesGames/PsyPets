<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Wishing Well';
$require_petload = 'no';
$THIS_ROOM = 'Wishing Well';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/houselib.php';
require_once 'commons/questlib.php';

if(!addon_exists($house, 'Wishing Well'))
{
  header('Location: /myhouse.php');
  exit();
}

$well_moneys = get_quest_value($user['idnum'], 'wishing well money');
$well_level = get_quest_value($user['idnum'], 'wishing well size');
$well_claims = get_quest_value($user['idnum'], 'wishing well claims');

if($well_moneys === false)
{
  add_quest_value($user['idnum'], 'wishing well money', 0);
  $well_moneys = get_quest_value($user['idnum'], 'wishing well money');

  if($well_moneys === false)
    die('weird database issues with your wishing well >_>');
}

if($well_level === false)
{
  add_quest_value($user['idnum'], 'wishing well size', 1);
  $well_level = get_quest_value($user['idnum'], 'wishing well size');

  if($well_level === false)
    die('weird database issues with your wishing well >_>');
}

if($well_claims === false)
{
  add_quest_value($user['idnum'], 'wishing well claims', 0);
  $well_claims = get_quest_value($user['idnum'], 'wishing well claims');

  if($well_claims === false)
    die('weird database issues with your wishing well >_>');
}

if($well_level['value'] == 1)
  $needed_moneys = 500;
else
  $needed_moneys = 1000 * ($well_level['value'] - 1);

if($well_claims['value'] > 0)
  $cave_quest = get_quest_value($user['idnum'], 'hidden cave quest');

if($_POST['submit'] == 'Throw' && $well_claims['value'] == 0)
{
  $amount = (int)$_POST['amount'];

  if($amount < 1)
    $message = '<span class="failure">Enter an amount of money greater than zero to throw into the well...</span>';
  else if($amount > $user['money'])
    $message = '<span class="failure">You do not have that much money on-hand.</span>';
  else
  {
    $well_moneys['value'] += $amount;

    update_quest_value($well_moneys['idnum'], $well_moneys['value']);
    
    take_money($user, $amount, 'Tossed into a Wishing Well');
    $user['money'] -= $amount;

    if($well_moneys['value'] >= $needed_moneys)
    {
      $well_level['value']++;
      $well_claims['value']++;
      
      update_quest_value($well_level['idnum'], $well_level['value']);
      update_quest_value($well_claims['idnum'], $well_claims['value']);

      $cave_quest = get_quest_value($user['idnum'], 'hidden cave quest');

      $message = '<span class="success">You toss in the moneys, when suddenly...</span>';
    }
    else
      $message = '<span class="progress">You toss in the moneys...</span>';
  }
}
else if($_POST['submit'] == 'Claim' && $well_claims['value'] > 0)
{
  $claim = (int)$_POST['prize'];
  
  if($claim < 1 || $claim > 4 || ($claim == 4 && $well_level['value'] <= 3))
    $message = '<span class="failure">The fairy looks confused and shakes her head.</span>';
  else
  {
    $well_claims['value']--;

    update_quest_value($well_claims['idnum'], $well_claims['value']);
    
    if($claim == 1)
    {
      $command = 'UPDATE monster_users SET rupees=rupees+20 WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
      fetch_none($command, 'updating rupee count');
      $user['rupees'] += 20;
    }
    else if($claim == 2)
      add_inventory($user['user'], '', 'Double Hammer', 'received from a Wishing Well fairy', $user['incomingto']);
    else if($claim == 3)
      add_inventory($user['user'], '', 'Really Enormously Tremendous Rock', 'received from a Wishing Well fairy', $user['incomingto']);
    else if($claim == 4)
    {
      add_inventory($user['user'], '', 'Mysterious Map', 'received from a Wishing Well fairy', $user['incomingto']);
      add_quest_value($user['idnum'], 'hidden cave quest', 1);

      $cave_quest = get_quest_value($user['idnum'], 'hidden cave quest');
    }

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Received a Small Happiness', 1);

    $message = '"<img src="//' . $SETTINGS['static_domain'] . '/gfx/ancientscript/fairyoutro.png" class="inlineimage" />," the fairy says before vanishing.';
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user['display'] ?>'s House &gt; Wishing Well</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Wishing Well</h4>
<?php
if($message)
  echo '<p>' . $message . '</p>';

room_display($house);

if($well_claims['value'] > 0)
{
  $HAMMER = array('itemname' => 'Double Hammer', 'graphictype' => 'bitmap', 'graphic' => 'hammer/double.png');
  $LARGE_ROCK = array('itemname' => 'Really Enormously Tremendous Rock', 'graphictype' => 'bitmap', 'graphic' => 'rock_enormous.png');
  $MYSTERIOUS_MAP = array('itemname' => 'Mysterious Map', 'graphictype' => 'bitmap', 'graphic' => 'map_mysterious.png');
?>
     <h5>The Appearance of a Fairy</h5>
     <a href="/npcprofile.php?npc=Wishing+Well+Fairy"><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/npcs/fairy2.png" alt="Wishing Well Fairy" align="right" width="320" height="410" /></a>
<?php include 'commons/dialog_open.php'; ?>
     <p><img src="//<?= $SETTINGS['static_domain'] ?>/gfx/ancientscript/fairyintro.png" /></p>
<?php include 'commons/dialog_close.php'; ?>
     <form method="post">
     <table>
      <tr class="titlerow">
       <th></th><th></th><th>Wish</th>
      </tr>
      <tr>
       <td><input type="radio" name="prize" value="1" /></td>
       <td class="centered"><img src="/gfx/items/rupee_green.png" width="16" height="32" alt="" /></td>
       <td>20 Rupees</td>
      </tr>
      <tr class="altrow">
       <td><input type="radio" name="prize" value="2" /></td>
       <td class="centered"><?= item_display($HAMMER) ?></td>
       <td>Double Hammer</td>
      </tr>
      <tr>
       <td><input type="radio" name="prize" value="3" /></td>
       <td class="centered"><?= item_display($LARGE_ROCK) ?></td>
       <td>Really Enormously Tremendous Rock</td>
      </tr>
<?php
  if($well_level['value'] > 3 && $cave_quest === false)
  {
?>
      <tr class="altrow">
       <td><input type="radio" name="prize" value="4" /></td>
       <td class="centered"><?= item_display($MYSTERIOUS_MAP) ?></td>
       <td>Mysterious Map</td>
      </tr>
<?php
  }
?>
     </table>
     <p><input type="submit" name="submit" value="Claim" /></p>
     </form>
<?php
}
else
{
?>
     <h5>Throw In Some Moneys</h5>
<?php
  if($well_moneys['value'] > 0)
    echo '<p>Total thrown in to date: ' . $well_moneys['value'] . '<span class="money">m</span></p>';
?>
     <form method="post">
     <p><input name="amount" size="6" maxlength="6" /> <input type="submit" name="submit" value="Throw" /></p>
     </form>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
