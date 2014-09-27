<?php
require_once 'commons/init.php';

$wiki = 'Flea_Market';
$require_petload = 'no';

require_once "commons/dbconnect.php";
require_once "commons/rpgfunctions.php";
require_once "commons/sessions.php";
require_once "commons/grammar.php";
require_once "commons/inventory.php";
require_once "commons/formatting.php";

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: ./ltc.php?dialog=2');
  exit();
}

$page = (int)$_GET['page'];

$command = 'SELECT COUNT(idnum) AS c FROM monster_users WHERE openstore=\'yes\'';
$data = fetch_single($command, 'fetching open store count');

$num_stores = (int)$data['c'];

if($num_stores > 0)
{
  $num_pages = ceil($num_stores / 20);

  if($page < 1 || $page > $num_pages)
    $page = 1;

  $command = 'SELECT storename,display FROM monster_users WHERE openstore=\'yes\' ORDER BY display ASC LIMIT ' . (($page - 1) * 20) . ',20';
  $stores = fetch_multiple($command, 'fetching page ' . $page . ' of open stores');

  $page_list = paginate($num_pages, $page, '/fleamarket/viewall.php?page=%s');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Flea Market &gt; Complete Listing</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/adrate3.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php include 'commons/bcmessage2.php'; ?>
     <h4>Flea Market</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/fleamarket/">Flea Market</a></li>
      <li><a href="/favorstores.php">Custom Item Market</a></li>
     </ul>
     <ul>
      <li><a href="/mystore.php">Manage my store</a></li>
      <li><a href="/fleamarket/">Search the Flea Market for an item for sale</a></li>
     </ul>
     <h5>Complete Listing</h5>
<?php
if($num_stores > 0)
{
  echo $page_list;

  echo '
    <table>
     <thead>
      <tr class="titlerow"><th>Store Name</th><th>Owner</th></tr>
     </thead>
     <tbody>
  ';

  $rowclass = begin_row_class();

  foreach($stores as $store)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td><a href="/userstore.php?user=' . link_safe($store['display']) . '">' . $store['storename'] . '</a></td>
       <td>' . resident_link($store['display']) . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '
     </tbody>
    </table>
  ';
  
  echo $page_list;
}
else
  echo '<p>There are no open stores!</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
