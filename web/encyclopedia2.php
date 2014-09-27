<?php
$require_login = 'no';
$whereat = 'encyclopedia';
$wiki = 'Encyclopedia';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/equiplib.php';
require_once 'commons/sellermarketlib.php';
require_once 'commons/encyclopedialib.php';

$itemname = trim(unlink_safe($_GET['item']));
$itemid = (int)$_GET['i'];

if($itemid > 0)
{
  $item = get_item_byid($itemid);
  $itemname = $item['itemname'];
}
else
{
  $item = get_item_byname($itemname);
  $itemid = $item['idnum'];
}

if(!$item || $itemid < 1 || ($item['custom'] == 'secret' && $user['admin']['clairvoyant'] != 'yes'))
{
  header('Location: /encyclopedia.php?msg=163');
  exit();
}

if($_POST['action'] == 'Update Cache' && $admin['manageitems'] == 'yes')
{
  get_item_byname($item['itemname'], true);
  get_item_byid($item['idnum'], true);
  
  header('Location: /encyclopedia2.php?i=' . $item['idnum']);
  exit();
}

// disable encyclopedia popup on this page
$user['encyclopedia_popup'] = 'no';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Item Encyclopedia &gt; <?= $item['itemname'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? '<p style="color:blue;">' . $check_message . '</p>' : '') ?>
     <div style="float:right;">
     <form action="encyclopedia.php" method="get">
     <p><input name="itemname" type="search" value="<?= $_POST['itemname'] ?>" maxlength="64" style="width:192px;" /> <input type="hidden" name="submit" value="Search" /><input type="submit" value="Search" /></p>
     </form>
     </div>

     <h4><a href="/encyclopedia.php">Item Encyclopedia</a> &gt; <?= $item['itemname'] ?></h4>
<?php
if($admin['clairvoyant'] == 'yes')
{
  if($item['admin_notes'] != '')
    echo '<p>' . $item['admin_notes'];
  else
    echo '<p>No admin notes.';

  if($admin['manageitems'] == 'yes')
    echo ' (<a href="/admin/edititem.php?id=' . $item['idnum'] . '">edit</a>)';

  echo '</p>';
}

if($admin['manageitems'] == 'yes')
{
  echo '
    <form method="post">
    <p><input type="submit" name="action" value="Update Cache" class="bigbutton" /></p>
    </form>
  ';
}

RenderEncyclopediaItem($item, $user, $userpets);
?>
     <hr />
     <h5>Search</h5>
     <form action="/encyclopedia.php" method="get">
     <p><input name="itemname" type="text" placeholder="item name" maxlength="64" style="width:192px;" /> <input type="hidden" name="submit" value="Search" /><input type="submit" value="Search" /></p>
     </form>
<?php
if($user['admin']['clairvoyant'] == 'yes')
{
  $make['binding'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_bindings WHERE makes=' . quote_smart($item['itemname']));
  $make['carpentry'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_carpentry WHERE makes=' . quote_smart($item['itemname']));
  $make['chemistry'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_chemistry WHERE makes=' . quote_smart($item['itemname']));
  $make['handicraft'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_crafts WHERE makes=' . quote_smart($item['itemname']));
  $make['gardening'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_gardening WHERE makes=' . quote_smart($item['itemname']));
  $make['electrical'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_inventions WHERE makes=' . quote_smart($item['itemname']));
  $make['jewelry'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_jewelry WHERE makes=' . quote_smart($item['itemname']));
  $make['mechanical'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_mechanics WHERE makes=' . quote_smart($item['itemname']));
  $make['leather'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_leatherworks WHERE makes=' . quote_smart($item['itemname']));
  $make['painting'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_paintings WHERE makes=' . quote_smart($item['itemname']));
  $make['sculpture'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_sculptures WHERE makes=' . quote_smart($item['itemname']));
  $make['smith'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_smiths WHERE makes=' . quote_smart($item['itemname']));
  $make['tailory'] = $database->FetchMultiple('SELECT difficulty,ingredients FROM psypets_tailors WHERE makes=' . quote_smart($item['itemname']));
?>
     <hr />
     <h5>Pet Crafts</h5>
     <ul>
<?php
  $user['encyclopedia_popup'] = 'yes';

  foreach($make as $type=>$list)
  {
    foreach($list as $data)
    {
      $items = explode(',', $data['ingredients']);
      $item_links = array();
      
      foreach($items as $item_name)
        $item_links[] = item_text_link($item_name);

      echo '<li>level-' . $data['difficulty'] . ' <a href="/admin/projecteditor.php?edit=' . $type . '">' . $type . '</a>: ' . implode(', ', $item_links) . '</li>';
    }
  }

  echo '</ul>';
}

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
