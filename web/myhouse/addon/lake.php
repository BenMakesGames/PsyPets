<?php
require_once 'commons/init.php';

$whereat = 'home';
$wiki = 'Lake';
$THIS_ROOM = 'Lake';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';
require_once 'commons/lakelib.php';
require_once 'commons/utility.php';
require_once 'commons/moonphase.php';
require_once 'commons/questlib.php';

if(!addon_exists($house, 'Lake'))
{
  header('Location: /myhouse.php');
  exit();
}

$lake = get_lake_byuser($user['idnum']);
if($lake === false)
{
  create_lake($user['idnum']);
  $lake = get_lake_byuser($user['idnum']);
  if($lake === false)
    die('Failed to create your lake!  Try reloading this page; if the problem persists, contact <a href=\"admincontact.php\">an administrator</a>.');
}

$boats = take_apart(',', $lake['boats']);
$num_boats = count($boats);

if($_GET['duckies'] > 0)
{
  $message .= '<p class="success">' . $_GET['duckies'] . ' Rubber Duck' . ($_GET['duckies'] == 1 ? 'y has' : 'ies have') . ' been added to your Lake.</p>';

  $badges = get_badges_byuserid($user['idnum']);

  if($badges['bathtime'] == 'no' && $lake['duckies'] >= 100)
  {
    set_badge($user['idnum'], 'bathtime');
    $message .= '<p class="success">You received the Bathtime Badge!</p>';
  }
}

$lady = get_quest_value($user['idnum'], 'lady of the lake');

if($lady === false)
{
  if(in_array('Swan Boat', $boats) && $lake['duckies'] >= 20 && $lake['monster'] != 'no')
  {
    $lady_of_the_lake = true;
    add_inventory($user['user'], 'lotl', 'Nivaine\'s Ring', 'Given to you by the Lady of the Lake', $user['incomingto']);
    add_quest_value($user['idnum'], 'lady of the lake', 1);
  }
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Lake</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Lake</h4>
<?php
echo $message;

room_display($house);

if($lady_of_the_lake)
{
  if($lake['duckies'] < 100)
    $ducky_note = 'It could use more Rubber Duckies, but otherwise it\'s very nice!';
  else
    $ducky_note = 'So many Rubber Duckies, too!  I love those little things...';

  // The Lady of the Lake
  echo '<img src="/gfx/npcs/lotl.png" align="right" width="300" height="250" alt="(The Lady of the Lake)" />';

  include 'commons/dialog_open.php';
?>
<p>Oh, hi!</p>
<p>I like what you've done with my lake!  <?= $ducky_note ?></p>
<p>Anyway, I wanted to give you this magic ring.  You know, for everything you've done.  It's really useful, just ask Lancelot!  I gave him one once, too, after he -- oh!  Um!  *blushes*  Nevermind!  <i class="size8">(Oh, god!)</i></p>
<p>Ah!  Um!  Well, sorry for interrupting!  I'm sure you're quite busy!</p>
<p>I'm going now!</p>
<p>Bye!</p>
<p><i>(You received Nivaine's Ring!  You can find it in <?= $user['incomingto'] ?>.)</i></p>
<?php
  include 'commons/dialog_close.php';
}

$options[] = '<a href="/myhouse/addon/lake_addduckies.php">Add Rubber Duckies</a>';

if($num_boats < 6)
  $options[] = '<a href="/myhouse/addon/lake_addboat.php">Add boats</a>';

if($lake['monster'] == 'no')
  $options[] = '<a href="/myhouse/addon/lake_addmonster.php">Add monster</a>';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

echo '<h5>Rubber Duckies (' . $lake['duckies'] . ')</h5>';

echo '<h5>Boats (' . $num_boats . ' / 6)</h5>' .
     '<p>The boats you have in your Lake determine the effectiveness of the "Play in Lake" half-hourly action.</p>';

if($num_boats > 0)
{
?>
<table>
 <thead>
  <tr class="titlerow">
   <th></th><th></th><th>Boat</th>
  </tr>
 </thead>
 <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($boats as $i=>$boat)
  {
    $details = get_item_byname($boat);
?>
  <tr class="<?= $rowclass ?>">
   <td><a href="/myhouse/addon/lake_remboat.php?i=<?= ($i + 1) ?>">Take boat</a></td>
   <td><?= item_display($details, '') ?></td>
   <td><?= $boat ?></td>
  </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
 </tbody>
</table>
<?php
}
else
  echo '<p>There are no boats on your lake <img src="/gfx/emote/aw.gif" width="16" height="16" alt="[sad]" /></p>';

if($lake['monster'] != 'no')
{
  $monster = get_pet_byid($lake['monster']);
?>
<h5>Monster</h5>
<table>
 <tr class="titlerow">
  <th></th><th></th><th>Monster</th><th>Presence</th>
 </tr>
 <tr>
  <td><?php if(strstr($monster['graphic'], '/') !== false) echo '<a href="/myhouse/addon/lake_reclaimmonster.php">Call back</a>'; ?></td>
  <td><a href="/petprofile.php?petid=<?= $lake['monster'] ?>"><img src="/gfx/pets/<?= $monster['graphic'] ?>" width="48" height="48" alt="" border="0" /></a></td>
  <td><?= $monster['petname'] ?></td>
  <td><?= ucfirst(monster_description($monster)) ?></td>
 </tr>
</table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
