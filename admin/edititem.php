<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/equiplib.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/blimplib.php';

function wiki_link($itemname)
{
  $link = str_replace(array('#', '&', '+', '"'), array('no. ', ' and ', ' ', '%22'), $itemname);
  $link = trim($link);
//  $link = link_safe($link);

  return '<a href="http://' . $SETTINGS['wiki_domain'] . '/' . $link . '">View PsyHelp entry</a>';
}

$itemid = (int)$_GET['id'];

$item = get_item_byid($itemid);

if($item === false)
{
  header('Location: /encyclopedia.php');
  exit();
}

if($admin['manageitems'] != 'yes')
{
  header('Location: /encyclopedia2.php?i=' . $itemid);
  exit();
}

if($_POST['action'] == 'update_enc_entry')
{
  $command = 'UPDATE monster_items SET enc_entry=' . quote_smart($_POST['enc_entry']) . ' WHERE idnum=' . $itemid . ' LIMIT 1';
  $database->FetchNone($command, 'updating item entry');
  
  header('Location: /encyclopedia2.php?i=' . $itemid);
  exit();
}

if($_POST['action'] == 'update_admin_notes')
{
  $command = 'UPDATE monster_items SET admin_notes=' . quote_smart($_POST['admin_notes']) . ' WHERE idnum=' . $itemid . ' LIMIT 1';
  $database->FetchNone($command, 'updating admin notes');

  header('Location: /encyclopedia2.php?i=' . $itemid);
  exit();
}

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Admin Tools &gt; Item Editor &gt; <?= $item['itemname'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Admin Tools</a> &gt; Item Editor &gt; <?= $item['itemname'] ?> <?php echo $CUSTOM_DESC[$item['custom']]; ?></h4>
     <ul>
      <li><?= wiki_link($item['itemname']) ?></li>
      <li><a href="/encyclopedia2.php?i=<?= $itemid ?>">View Enyclopedia entry</a></li>
     </ul>
     <table class="nomargin">
      <tr>
       <td valign="top"><img src="/gfx/items/<?= $item['graphic'] ?>" height="32" /></td>
       <td>
        <p><?= $item['itemname'] ?><br /><?= $item['itemtype'] ?></p>
       </td>
      </tr>
     </table>
     <hr />
     <h5>Encyclopedia Entry</h5>
     <form method="post">
     <p><textarea name="enc_entry" cols="80" rows="6"><?= htmlentities($item['enc_entry']) ?></textarea></p>
     <p><input type="hidden" name="action" value="update_enc_entry" /><input type="submit" value="Update" /></p>
     </form>
     <h5>Administrative Notes</h5>
     <form method="post">
     <p><textarea name="admin_notes" cols="80" rows="2"><?= htmlentities($item['admin_notes']) ?></textarea></p>
     <p><input type="hidden" name="action" value="update_admin_notes" /><input type="submit" value="Update" /></p>
     </form>
<?php
if($user['admin']['seedebug'] == 'yes')
{
  require_once 'commons/sqldumpfunc.php';
?>
     <hr />
     <h5>Raw Information</h5>
<?php
  dump_sql_results($item);
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
