<?php
$whereat = 'volcano';
$wiki = 'Volcano';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/globals.php';
require_once 'commons/messages.php';
require_once 'commons/questlib.php';

if($user['show_volcano'] != 'yes')
{
  header('Location: ./404.php');
  exit();
}

if($_POST['action'] == 'Burn!')
{
  $itemids = array();

  foreach($_POST as $key=>$value)
  {
    if(substr($key, 0, 2) == 'i_')
      $itemids[] = (int)substr($key, 2);
  }
  
  if(count($itemids) > 0)
  {
    $command = '
      DELETE FROM monster_inventory
      WHERE
        idnum IN (' . implode(',', $itemids) . ')
        AND user=' . quote_smart($user['user']) . '
        AND location=\'storage\'
      LIMIT ' . count($itemids);
    $database->FetchNone($command, 'deleting items.  FOR SERIOUS.');
    
    $deleted_items = $database->AffectedRows();

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Tossed an Item into The Volcano', $deleted_items);
    
    if($deleted_items == 1)
      $dialog = '<p>I hope you won\'t want that back later.  Because you can\'t have it.</p>';
    else
      $dialog = '<p>I hope you won\'t want any of those ' . $deleted_items . ' item' . ($deleted_items != 1 ? 's were' : ' was') . ' back later.  Because you can\'t have them.</p>';
  }
  else
    $dialog = '<p>So, what?  You\'re not throwing any in after all?</p><p>Well, I guess I shouldn\'t complain, but why\'d you come all the way up here, then?  Just to mess with me?</p>';

  $options[] = '<a href="?dialog=whysoangry">Ask her why her panties are in a twist</a>';
}
else if($_GET['dialog'] == 'whysoangry')
{
  $dialog = '
    <p>... what.</p>
    <p><em>I live in a volcano.</em></p>
    <p>You do know how <em>hot</em> volcanoes are, right?  Don\'t you have that school thing you humans do, or whatever?</p>
    <p class="size8">Ki Ri Kashu, why do I bother...</p>
  ';
}
else
{
  $dialog = '
    <p>Oh goodie.  Let me guess: you\'re here to get rid of something you don\'t want anymore; some super-valuable or rare item that, for <em>whatever reason</em>, you can\'t just <em>throw away</em> - heaven forbid you use the <em>trash</em> like everyone else - no, you have to be all <em>dramatic</em> about it and come to a volcano - <em>my</em> volcano - to get rid of it.</p>
    <p>Oh, no, no: don\'t let <em>me</em> stop you.  I wouldn\'t want your long hike up here to have been for nothing.  Whatever it is, just <em>throw it in</em> and be done with it.</p>
  ';

  $options[] = '<a href="?dialog=whysoangry">Ask her why her panties are in a twist</a>';
}

$items = $database->FetchMultiple('
  SELECT
    a.idnum,
    a.itemname,
    b.graphictype,
    b.graphic
  FROM monster_inventory AS a
  LEFT JOIN monster_items AS b
  ON a.itemname=b.itemname
  WHERE
    a.user=' . quote_smart($user['user']) . '
    AND a.location=\'storage\'
    AND b.custom!=\'no\'
    AND b.cursed=\'no\'
    AND b.questitem=\'no\'
  ORDER BY a.itemname ASC
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Volcano</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Volcano</h4>
<?php
echo '<a href="npcprofile.php?npc=Volcano Spirit"><img src="gfx/npcs/volcanospirit.png" align="right" width="350" height="470" alt="(Volcano Spirit)" /></a>';

include 'commons/dialog_open.php';

echo $dialog;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>', implode('</li><li>', $options), '</li></ul>';

echo '<p><i>(Only items in your storage which cannot be normally thrown away - including custom and monthly items - are listed below.  <strong>Use The Volcano with care!</strong>)</i></p>';
  
if(count($items) > 0)
{
?>
<form method="post" onsubmit="return confirm('Make absolutely sure you selected only what you want thrown away!  You can\'t get this stuff back!');">
<table>
 <tr class="titlerow">
  <th></th>
  <th></th>
  <th>Item</th>
 </tr>
<?php
  $rowclass = begin_row_class();

  foreach($items as $item)
  {
?>
 <tr class="<?= $rowclass ?>">
  <td><input type="checkbox" name="i_<?= $item['idnum'] ?>" /></td>
  <td align="center"><?= item_display($item, '') ?></td>
  <td><?= $item['itemname'] ?></td>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
</table>
<p><input type="submit" name="action" value="Burn!" /></p>
<?php
}
else
{
?>
     <p>You don't have any Volcano-able items in Storage.</p>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
