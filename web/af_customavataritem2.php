<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/graphiclibrary.php';
require_once 'commons/favorlib.php';

if($now_year > 2010 || ($now_year == 2010 && $now_month >= 4))
  $favor_cost = 500;
else
  $favor_cost = 500;

if($_POST['action'] == 'make_mah_item' && $user['favor'] >= $favor_cost)
{
  $itemgraphicid = (int)$_POST['itemgraphic'];
  $avatargraphicid = (int)$_POST['avatargraphic'];
  $itemname = trim($_POST['itemname']);
  $itemtype = trim($_POST['itemtype']);
  $itemaction = trim($_POST['itemaction']);
  $itemflavor = trim($_POST['itemflavor']);

  $item_gfx = get_graphic_byid($itemgraphicid);
  $avatar_gfx = get_graphic_byid($avatargraphicid);

  if($item_gfx === false || $item_gfx['h'] != 32)
  {
    $errored = true;
    $error_msgs[] = 'Please select an item graphic.';
  }
  else if($item_gfx['recipient'] > 0 && $item_gfx['recipient'] != $user['idnum'])
  {
    $errored = true;
    $error_msgs[] = 'Please select an item graphic.';
  }

  if($avatar_gfx === false || $avatar_gfx['h'] != 48)
  {
    $errored = true;
    $error_msgs[] = 'Please select an avatar graphic.';
  }
  else if($avatar_gfx['recipient'] > 0 && $avatar_gfx['recipient'] != $user['idnum'])
  {
    $errored = true;
    $error_msgs[] = 'Please select an avatar graphic.';
  }

  if(strlen($itemname) < 2 || strlen($itemname) > 48)
  {
    $errored = true;
    $error_msgs[] = 'You forgot to name your item (or it\'s simply too short - 2 character minimum).';
  }
  else if(strpos($itemname, '_') !== false)
  {
    $errored = true;
    $error_msgs[] = 'Item names may not contain an underscore.  Sorry.';
  }
  else
  {
    $command = 'SELECT * FROM `monster_items` WHERE `itemname`=' . quote_smart($itemname) . ' LIMIT 1';
    $existing_item = $database->FetchSingle($command, 'fetching existing item');

    if($existing_item !== false)
    {
      $errored = true;
      $error_msgs[] = 'That item name is already in use.';
    }
  }
   
  if(strlen($itemtype) < 8 || strlen($itemtype) > 32)
  {
    $errored = true;
    $error_msgs[] = 'You forgot the item type (or it\'s simply too short - 8 character minimum).';
  }
  else if(preg_match("/[^a-zA-Z\/]/", $itemtype))
  {
    $errored = true;
    $error_msgs[] = 'The item type must only contain letters and slases (not even spaces are okay).';
  }

  if(strlen($itemaction) < 2 || strlen($itemaction) > 16)
  {
    $errored = true;
    $error_msgs[] = 'You forgot the item\'s action name (or it\'s simply too short - 2 character minimum).';
  }
  else if(preg_match("/;/", $itemaction))
  {
    $errored = true;
    $error_msgs[] = 'Oh, I forgot to mention: semi-colons are not allowed in the item\'s action name.  Sorry.';
  }

  if(strlen($itemflavor) < 8 || strlen($itemflavor) > 64)
  {
    $errored = true;
    $error_msgs[] = "You forgot the item's action description (or it's simply too short - 8 character minimum).";
  }
  else if(preg_match("/;/", $itemflavor))
  {
    $errored = true;
    $error_msgs[] = 'Oh, I forgot to mention - semi-colons are not allowed.  Sorry.';
  }

  if($errored == false)
  {
    $q_itemname = quote_smart($itemname);
    $q_itemtype = quote_smart($itemtype);
    $q_graphic = quote_smart('../../' . $item_gfx['url']);
    $q_action = quote_smart(
      $itemaction . ';masks/generic.php;../../' . $avatar_gfx['url'] . ';' . $itemflavor
    );

    // create the item
    $command = "INSERT INTO monster_items (`itemname`, `itemtype`, `custom`, `bulk`, `weight`, `graphic`, `action`, `rare`, `nosellback`) VALUES " .
               "($q_itemname, $q_itemtype, 'yes', '2', '2', $q_graphic, $q_action, 'yes', 'yes')";
    $database->FetchNone($command, 'creating item record');

    // create the inventory item reference
    $command = "INSERT INTO monster_inventory (`user`, `creator`, `itemname`, `message`, `location`, `changed`) VALUES " .
               "(" . quote_smart($user['user']) . ", " . quote_smart('u:' . $user['idnum']) . ", $q_itemname, 'Created by the Avatar Item Builder', 'storage/incoming', $now)";
    $database->FetchNone($command, 'creating inventory item');

    $id = $database->InsertID();

    flag_new_incoming_items($user['user']);

    spend_favor($user, $favor_cost, 'custom avatar item - "' . $itemname . '"', $id);

    $uploader1 = get_user_byid($item_gfx['uploader']);
    $uploader2 = get_user_byid($avatar_gfx['uploader']);

    record_graphic_use($itemgraphicid, $item_gfx, $uploader1);
    record_graphic_use($avatargraphicid, $avatar_gfx, $uploader2);

    if($uploader1 !== false && $uploader1["user"] === $uploader2["user"])
    {
      $badges = get_badges_byuserid($uploader1['idnum']);
      if($badges['artist'] == 'no')
      {
        set_badge($uploader1['idnum'], 'artist');
        $extra = '<br /><br />{i}(You won the Artist Badge!){i}';
      }

      psymail_user($uploader1['user'], 'psypets', 'Your graphics from the Graphic Library were used!', '{r ' . $user["display"] . "} has used your {i}" . $item_gfx['title'] . '{/} and {i}' . $avatar_gfx['title'] . "{/} graphics to make a custom avatar item: $itemname.$extra");
    }
    else
    {
      if($uploader1 !== false)
      {
        $badges = get_badges_byuserid($uploader1['idnum']);
        if($badges['artist'] == 'no')
        {
          set_badge($uploader1['idnum'], 'artist');
          $extra1 = '<br /><br />{i}(You won the Artist Badge!){i}';
        }

        psymail_user($uploader1['user'], 'psypets', "Your graphic from the Graphic Library was used!", "{r " . $user["display"] . "} has used your {i}" . $item_gfx["title"] . "{/} graphic to make a custom item: $itemname.$extra1");
      }

      if($uploader2 !== false)
      {
        $badges = get_badges_byuserid($uploader2['idnum']);
        if($badges['artist'] == 'no')
        {
          set_badge($uploader2['idnum'], 'artist');
          $extra2 = '<br /><br />{i}(You won the Artist Badge!){i}';
        }

        psymail_user($uploader2['user'], 'psypets', 'Your graphic from the Graphic Library was used!', '{r ' . $user['display'] . '} has used your {i}' . $avatar_gfx['title'] . "{/} graphic as a custom avatar.$extra2");
      }
    }

    require_once 'commons/dailyreportlib.php';
    record_daily_report_stat('Someone Made an Avatar Item', 1);

    $message = 'Success!  The item is waiting for you in Incoming.';
    $_POST = array();
  }
}

$itemgraphics = get_graphics_byuserid($user["idnum"], 32);
$avatargraphics = get_graphics_byuserid($user["idnum"], 48);

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; Pawn Shop &gt; Custom Avatar Item Builder</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="pawnshop.php">Pawn Shop</a> &gt; Custom Avatar Item Builder</h4>
     <ul class="tabbed">
      <li><a href="pawnshop.php">Pawn Shop</a></li>
      <li class="activetab"><a href="af_customavataritem2.php">Custom Avatar Item Builder</a></li>
     </ul>
<?php
echo '<a href="/npcprofile.php?npc=Tony+Cables"><img src="gfx/npcs/tony.png" align="right" width="350" height="305" alt="(Tony "Shady" Cables)" /></a>';

include 'commons/dialog_open.php';

if(count($error_msgs) > 0)
{
  foreach($error_msgs as $error_message)
    echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
}
else if($message)
  echo "<p style=\"color:green;\">" . $message . "</p>\n";
else
  echo '<p>Hey, I understand how it is: sometimes you gotta\' lay low; blend in; disappear.  What you need is a <em>disguise</em>...</p><p>I can get one together for you, but it\'s not gonna be cheap.  Hey, don\'t give me that look.  Providing this service comes with some risks, you know?  It\'ll cost you ' . $favor_cost . ' Favor, and not a penny less.  If you don\'t have any, <a href="buyfavors.php">go pick some up</a>.  I can wait.</p>';

include 'commons/dialog_close.php';
?>
     <p>You have <?= $user['favor'] ?> Favor.  Creating an avatar-changing item will cost <?= $favor_cost ?>.</p>
     <ul>
      <li><a href="/buyfavors.php">Support PsyPets; get Favor</a></li>
     </ul>
<?php
if($user['favor'] >= $favor_cost)
{
  $graphicsOK = true;

  if(count($itemgraphics) < 1)
  {
    echo "<p>There are no item graphics available to you at this time.</p>\n";
    $graphicsOK = false;
  }

  if(count($avatargraphics) < 1)
  {
    echo "<p>There are no avatar graphics available to your at this time.</p>\n";
    $graphicsOK = false;
  }

  if($graphicsOK)
  {
?>
     <p>From here you can make an avatar-changing item.  Its name, appearance, the graphic it changes your avatar into, and even the name of the action on the item, are all customizable.</p>
     <form action="af_customavataritem2.php" method="post">
     <h5>Item Name</h6>
     <p><input type="text" name="itemname" value="<?= $_POST["itemname"] ?>" maxlength="48" /></p>
     <h5>Item Classification</h5>
     <p>For example: "clothing/hat", or "whateverNoOneCares" (without the quotes).  Letters and slashes only.</p>
     <p><input type="text" name="itemtype" value="<?= $_POST["itemtype"] ?>" maxlength="32" />
     <h5>Item Graphic</h5>
     <p>Choose a graphic for the item itself.  This is <strong>not</strong> the graphic your avatar will become when using the item.</p>
<?php include 'commons/gl_warning.php'; ?>
<table>
<tr>
<?php
    $i = 0;
    foreach($itemgraphics as $graphic)
    {
      if($i % 4 == 0 && $i > 0)
        echo "</tr><tr>\n";
?>
<td align="center">
 <table><tr><td><img src="<?= $graphic["url"] ?>" /></td><td bgcolor="#f0f0f0"><img src="<?= $graphic["url"] ?>" /></td></tr></table>
 <input type="radio" name="itemgraphic" value="<?= $graphic["idnum"] ?>" />
</td>
<?php
      ++$i;
    }
?>
</tr>
</table>
       <h5>Avatar Graphic</h5>
       <p>Choose the graphic your avatar will become upon using the item.</p>
<?php include 'commons/gl_warning.php'; ?>
<table>
<tr>
<?php
    $i = 0;
    foreach($avatargraphics as $graphic)
    {
      if($i % 4 == 0 && $i > 0)
        echo "</tr><tr>\n";
?>
<td align="center">
 <table><tr><td><img src="<?= $graphic["url"] ?>" /></td><td bgcolor="#f0f0f0"><img src="<?= $graphic["url"] ?>" /></td></tr></table>
 <input type="radio" name="avatargraphic" value="<?= $graphic["idnum"] ?>" />
</td>
<?php
      ++$i;
    }
?>
</tr>
</table>
<h5>Item Action</h5>
<p>For example: "Read", "Wear", "Drink" (again, without the quotes).</p>
<p><input type="text" name="itemaction" value="<?= $_POST["itemaction"] ?>" maxlength="12" /></p>
<h4>Use Description</h4>
<p>Text seen when the item is used.  For example: "You put on the mask." or "Your skin peels away, revealing a new form." (And you guessed it: quotes not necessary :P)</p>
<p><input type="text" name="itemflavor" value="<?= $_POST["itemflavor"] ?>" maxlength="56" size="40" /></p>
<p><input type="hidden" name="action" value="make_mah_item" /><input type="submit" value="Create!" /></p>
</form>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
