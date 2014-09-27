<?php
require_once 'commons/init.php';

$require_petload = 'no';
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/encryption.php';
require_once 'commons/formatting.php';
require_once 'commons/psypetsformatting.php';
require_once 'commons/globals.php';
require_once 'commons/profiles.php';

if($_POST['action'] == 'Do It' && $_POST['reset'] == 'yes')
{
  fetch_none('
    DELETE FROM psypets_profile_treasures
    WHERE userid=' . (int)$user['idnum'] . '
  ');
  
  $CONTENT['messages'][] = '<p class="success">IT IS DONE.</p>';
}

$num_treasures = fetch_single('
  SELECT COUNT(idnum) AS c
  FROM psypets_profile_treasures
  WHERE userid=' . (int)$user['idnum'] . '
');

$treasure_count = (int)$num_treasures['c'];
$treasure_pages = ceil($treasure_count / 10);

$page = (int)$_GET['page'];

if($page < 1 || $page > $treasure_pages)
  $page = 1;

$treasures = fetch_multiple('
  SELECT b.itemname,b.graphic,b.graphictype,c.ranking
  FROM
    psypets_profile_treasures AS c
    LEFT JOIN monster_items AS b
      ON c.itemid=b.idnum
  WHERE
    c.userid=' . (int)$user['idnum'] . '
  ORDER BY c.ranking DESC
  LIMIT ' . (($page - 1) * 10) . ',10
');

if($treasure_pages > 1)
  $page_list = paginate($treasure_pages, $page, '?page=%s');

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Account &gt; Resident Profile &gt; Profile Items</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/myaccount/">My Account</a> &gt; <a href="/myaccount/profile.php">Resident Profile</a> &gt; Profile Items</h4>
  <ul class="tabbed">
   <li class="activetab"><a href="/myaccount/profile.php">Resident&nbsp;Profile</a></li>
   <li><a href="/myaccount/searchable.php">Searchable&nbsp;Profile</a></li>
   <li><a href="/myaccount/petprofile.php">Pet&nbsp;Profiles</a></li>
   <li><a href="/myaccount/display.php">Display&nbsp;Settings</a></li>
   <li><a href="/myaccount/behavior.php">Behavior&nbsp;Settings</a></li>
   <li><a href="/myaccount/security.php">Account&nbsp;Management</a></li>
   <li><a href="/myaccount/favorhistory.php">Favor&nbsp;History</a></li>
   <li><a href="/myaccount/contentcontrol.php">Content&nbsp;Control</a></li>
  </ul>
<?php
if($treasure_count > 0)
{
?>
  <div style="float:right; width:250px; padding: 10px; border: 1px dashed #999;">
  <form method="post">
  <p><input type="checkbox" name="reset" value="yes" /> Clear all profile item preferences</p>
  <center><input type="submit" name="action" value="Do It" /></center>
  </form>
  </div>
<?php
  echo $page_list;
?>
  <table>
   <thead>
    <tr>
     <th></th><th>Item</th><th>Ranking</th>
    </tr>
   </thead>
   <tbody>
<?php
  $rowclass = begin_row_class();

  foreach($treasures as $item)
  {
?>
    <tr class="<?= $rowclass ?>">
     <td class="centered"><?= item_display($item) ?></td>
     <td><?= $item['itemname'] ?></td>
     <td class="centered"><?= $item['ranking'] / 100 ?></td>
    </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
   </tbody>
  </table>
<?php
  echo $page_list;
}
else
  echo '<p>You have not chosen any items to display on your profile.  To add items, visit their <a href="/encyclopedia.php">encyclopedia entries</a>.  (You can view any item\'s encyclopedia entry by clicking on that item in your house, another store, or any where else items are shown.)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
