<?php
$require_login = 'no';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

$command = 'SELECT COUNT(userid) AS c FROM psypets_store_portraits';
$data = $database->FetchSingle($command, 'fetching portrait count');

$items = $data['c'];
$pages = ceil($data['c'] / 10);

$page = (int)$_GET['page'];

if($page < 1 || $page > $pages)
  $page = 1;

$command = 'SELECT userid FROM psypets_store_portraits ORDER BY timestamp DESC LIMIT ' . (($page - 1) * 10) . ',10';
$graphics = $database->FetchMultiple($command, 'fetching');

$page_list = paginate($pages, $page, 'shopkeepgallery.php?page=%s');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Flea Market &gt; Shop Keeper Gallery</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="/fleamarket/">Flea Market</a> &gt; Shop Keeper Gallery</h4>
     <ul><li><a href="mysketchbook.php">Draw your own!</a></li></ul>
<?= $page_list ?>
<?php
foreach($graphics as $graphic)
{
  $resident = get_user_byid($graphic['userid'], 'display');
  echo '<a href="userstore.php?user=' . link_safe($resident['display']) . '"><img src="shopkeep.php?id=' . $graphic['userid'] . '" width="350" height="500" style="float:left; margin-right:1em; margin-bottom:1em;" border="0" /></a>';
}
?>
<div style="clear:both;"></div>
<?= $page_list ?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
