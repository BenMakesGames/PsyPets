<?php
$wiki = 'My_Favor_Store';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/questlib.php';

if($user['license'] == 'no')
{
  header('Location: ./storage.php');
  exit();
}

$command = 'SELECT a.idnum,a.markup,b.graphic,b.graphictype,b.itemname,c.display FROM psypets_custom_item_store AS a LEFT JOIN monster_items AS b ON a.itemid=b.idnum LEFT JOIN monster_users AS c ON a.ownerid=c.idnum ORDER BY c.display ASC,b.itemname ASC';
$for_sale = $database->FetchMultiple($command, 'fetching customs for sale');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Custom Item Market</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php
include 'commons/header_2.php';

if($my_favor_store_tutorial_quest === false)
  include 'commons/tutorial/myfavorstore.php';
?>
     <h4>Flea Market &gt; Custom Item Market</h4>
     <ul class="tabbed">
      <li><a href="/fleamarket/">Flea Market</a></li>
      <li class="activetab"><a href="favorstores.php">Custom Item Market</a></li>
     </ul>
     <ul>
      <li><a href="myfavorstore.php">Manage my store</a></li>
     </ul>
     <table>
      <thead>
       <tr class="titlerow">
        <th></th><th>Item</th><th>Price (Favor)</th><th>Seller</th>
       </tr>
      </thead>
      <tbody>
<?php
$rowclass = begin_row_class();

foreach($for_sale as $item)
{
  echo '
    <tr class="' . $rowclass . '">
     <td class="centered">' . item_display($item) . '</td>
     <td>' . $item['itemname'] . '</td>
     <td class="righted"><a href="favorstore.php?resident=' . link_safe($item['display']) . '">' . (300 + $item['markup']) . '</a></td>
     <td>' . resident_link($item['display']) . '</td>
    </tr>
  ';
  
  $rowclass = alt_row_class($rowclass);
}
?>
      </tbody>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
