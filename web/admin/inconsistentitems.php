<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/formatting.php";

if($admin["manageitems"] != "yes")
{
  Header("Location: /admin/tools.php");
  exit();
}

if($_POST['action'] == 'cursedpemanent')
{
  $command = 'UPDATE monster_items SET nosellback=\'yes\' WHERE cursed=\'yes\'';
  $database->FetchNone(($command, 'fixing inconsistent items (2)');
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Inconsistent Items</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Inconsistent Items</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/admin/inconsistentitems.php">Inconsistent Items</a></li>
      <li><a href="/admin/marketexploits.php">Non-recyclable, Game-sellable Items</a></li>
     </ul>

<h5>Items with "NOSELLBACK" and "PAWNABLE":</h5>
<?php
$command = 'SELECT idnum,itemname FROM monster_items WHERE can_pawn_for=\'yes\' AND nosellback=\'yes\'';
$items = $database->FetchMultiple(($command, 'fetching inconsistent items (1)');

if(count($items) > 0)
{
?>
<table>
<tr class="titlerow">
 <th>Name</th>
</tr>
<?php
  $rowstyle = begin_row_class();

  foreach($items as $item)
  {
?>
<tr class="<?= $rowstyle ?>">
 <td><a href="encyclopedia2.php?i=<?= $item['idnum'] ?>"><?= $item['itemname'] ?></a></td>
</tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
?>
</table>
<?php
}
else
  echo '<ul><li>There are no such items!  Fantastic!</li></ul>';
?>
<h5>Items with "CURSED", but not "NOSELLBACK":</h5>
<?php
$command = 'SELECT idnum,itemname,cursed FROM monster_items WHERE cursed=\'yes\' AND nosellback=\'no\'';
$items = $database->FetchMultiple(($command, 'fetching inconsistent items (2)');

if(count($items) > 0)
{
?>
<table>
<tr class="titlerow">
 <th>Name</th>
 <th>Cursed?</th>
</tr>
<?php
  $rowstyle = begin_row_class();

  foreach($items as $item)
  {
?>
<tr class="<?= $rowstyle ?>">
 <td><a href="/encyclopedia2.php?i=<?= $item['idnum'] ?>"><?= $item['itemname'] ?></a></td>
 <td class="centered"><?= $item['cursed'] == 'yes' ? 'Y' : '' ?></td>
</tr>
<?php
    $rowstyle = alt_row_class($rowstyle);
  }
?>
</table>
<form action="/admin/inconsistentitems.php" method="post">
<p>Fix the above items by setting their NOSELLBACK flags: <input type="hidden" name="action" value="cursedpemanent" /><input type="submit" value="Fix" /></p>
</form>
<?php
}
else
  echo '<ul><li>There are no such items!  Fantastic!</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
