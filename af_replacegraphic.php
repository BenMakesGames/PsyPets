<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/graphiclibrary.php';

$as_user = $user['idnum'];

$custom_items = array();
$i = 1;

$command = 'SELECT * FROM psypets_favor_history WHERE userid=' . $as_user . ' ORDER BY timestamp DESC';
$history = $database->FetchMultiple($command, 'fetching favor history');

if(count($history) > 0)
{
  foreach($history as $favor)
  {
    if(substr($favor['favor'], 0, 15) == 'custom item - "')
      $itemname = substr($favor['favor'], 15, strlen($favor['favor']) - 16);
    else if(substr($favor['favor'], 0, 22) == 'custom avatar item - "')
      $itemname = substr($favor['favor'], 22, strlen($favor['favor']) - 23);
    else
      continue;

    if(!in_array($itemname, $custom_items))
    {
      $item = get_item_byname($itemname);
      $fn = $item['graphic'];
      if(substr($fn, 0, 6) == '../../')
      {
        if(!file_exists(substr($item['graphic'], 6)))
        {
          $custom_items[$i] = $itemname;
          $i++;
        }
      }
    }
  }
}

$itemgraphics = get_graphics_byuserid($user['idnum'], 32);

if($_POST['action'] == 'fixup')
{
  $num = (int)$_POST['itemnum'];
  $itemgraphicid = (int)$_POST['itemgraphic'];

  $error_msgs = array();
  $errored = false;

  $item_gfx = get_graphic_byid($itemgraphicid);

  if($item_gfx === false || $item_gfx['h'] != 32)
  {
    $errored = true;
    $error_message .= 'Please select an item graphic.<br />';
  }
  else if($item_gfx['recipient'] > 0 && $item_gfx['recipient'] != $user['idnum'])
  {
    $errored = true;
    $error_message .= 'Please select an item graphic.<br />';
  }

  if(!array_key_exists($num, $custom_items) || $num == 0)
  {
    $errored = true;
    $error_message .= 'Please select an item to repair. (' . $num . ')<br />';
  }
  
  if(!$errored)
  {
    $q_graphic = quote_smart('../../' . $item_gfx['url']);

    $item = get_item_byname($custom_items[$num]);

    $command = 'UPDATE monster_items SET graphic=' . $q_graphic . ' WHERE idnum=' . $item['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'repairing custom item graphic');

    $uploader = get_user_byid($item_gfx["uploader"], 'display,idnum,user');

    record_graphic_use($itemgraphicid, $item_gfx, $uploader);

    if($uploader !== false)
    {
      $badges = get_badges_byuserid($uploader['idnum']);
      if($badges['artist'] == 'no')
      {
        set_badge($uploader['idnum'], 'artist');
        $extra = '<br /><br />{i}(You won the Artist Badge!){i}';
      }

      psymail_user($uploader['user'], 'psypets', 'Your graphic from the Graphic Library was used!', '{r ' . $user['display'] . "} has used your {i}" . $item_gfx["title"] . "{/} graphic to repair the broken image on their " . $custom_items[$num] . ".$extra");
    }

    unset($custom_items[$num]);
    $itemgraphics = get_graphics_byuserid($user['idnum'], 32);

    $message = 'Alright!  The ' . $item['itemname'] . ' has been all patched up.  Any others items y\'need fixed up?';
  }
}

include 'commons/html.php';
?>
 <head>
  <title>PsyPets &gt; The Smithery &gt; Item Graphic Repair</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<img src="gfx/npcs/smithy.png" align="right" width="350" height="280" alt="(Nina the Smithy)" />
<?php
include 'commons/dialog_open.php';

if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
else if($message)
  echo "<p style=\"color:green;\">" . $message . "</p>\n";
else
{
?>
<p>If any of your custom item's graphics are missing because the graphic owner decided to remove them from the site, I can replace the graphic with another from the <a href="gl_browse.php">Graphics Library</a> <strong>at no cost</strong>.</p>
<p>This will fix all copies of the item in the game, no matter who owns them (and even if you don't currently own one at all).</p>
<?php
}

include 'commons/dialog_close.php';

if(count($custom_items) > 0)
{
  $rowstyle = begin_row_class();
?>
     <h6>Select Item</h6>
     <form action="af_replacegraphic.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th>Item</th>
      </tr>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="itemnum" value="0" checked /></td>
       <td><i class="dim">do nothing</i></td>
      </tr>
<?php
  foreach($custom_items as $num=>$name)
  {
    $rowstyle = alt_row_class($rowstyle);
?>
      <tr class="<?= $rowstyle ?>">
       <td><input type="radio" name="itemnum" value="<?= $num ?>" /></td>
       <td><a href="encyclopedia2.php?item=<?= link_safe($name) ?>"><?= $name ?></a></td>
      </tr>
<?php
  }
?>
     </table>
     <h6>Select Replacement Graphic</h6>

<?php include 'commons/gl_warning.php'; ?>
<table>
<tr class="titlerow">
 <th colspan="4">Graphics Library</th>
</tr>
<tr>
<?php
$i = 0;
foreach($itemgraphics as $graphic)
{
  if($i % 4 == 0 && $i > 0)
    echo "</tr><tr>\n";
?>
<td align="center" style="border-bottom: 1px solid #ccc; border-right: 1px solid #ccc;">
 <table><tr><td><img src="<?= $graphic['url'] ?>" /></td><td bgcolor="#f0f0f0"><img src="<?= $graphic['url'] ?>" /></td></tr></table>
 <input type="radio" name="itemgraphic" value="<?= $graphic['idnum'] ?>" />
</td>
<?php
  ++$i;
}
?>
</tr>
</table>

     <p><input type="hidden" name="action" value="fixup" /><input type="submit" value="Repair" /></p>
     </form>
<?php
}
else
  echo "<p>None of your custom items have broken images.</p>\n";
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
