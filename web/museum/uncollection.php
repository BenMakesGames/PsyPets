<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

if(array_key_exists('resident', $_GET))
{
  $resident_name = urldecode($_GET['resident']);

  $resident = get_user_bydisplay($resident_name);

  if($resident === false)
  {
    header('Location: /museum/wings.php');
    exit();
  }

  $title = $resident['display'] . '\'s Uncollection';
  $collection_link = '<a href="/museum/view.php?resident=' . urlencode($resident['display']) . '">' . $resident['display'] . '\'s Collection</a>';
  $you_have = $resident['display'] . ' has';
  
  $url_extra = 'resident=' . urlencode($resident['display']) . '&';
}
else
{
  $resident = $user;
  $title = 'My Uncollection';
  $collection_link = '<a href="/museum/">My Collection</a>';
  $you_have = 'you have';
  
  $other_links = '
    <li><a href="/museum/donate.php">Make Donation</a></li>
    <li><a href="/museum/exchange.php">Exchanges</a></li>
    <li><a href="/museum/displayeditor.php">My Displays</a></li>
  ';
}

$options = array();

$item_count = get_user_unmuseum_count($resident['idnum']);

if($item_count > 0)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $pages = paginate($max_pages, $page, '/museum/uncollection.php?' . $url_extra . 'page=%s');
}
else
  $page = 1;

$item_list = get_user_unmuseum_page($resident['idnum'], $page);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; <?= $title ?> (page <?= $page ?>)</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/museum/">The Museum</a> &gt; <?= $title ?></h4>
     <ul class="tabbed">
      <li><?= $collection_link ?></li>
      <li class="activetab"><a href="/museum/uncollection.php"><?= $title ?></a></li>
<?= $other_links ?>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo '<p>This is a list of all the items which ' . $you_have . ' <em>not</em> donated.</p>';

include 'commons/dialog_close.php';

if($item_count == 0)
  echo '<p>There isn\'t <em>anything</em> ' . $you_have . 'n\'t donated to The Museum!</p>';
else
{
  echo $pages .
       '<table>' .
       '<tr class="titlerow"><th></th><th>Item ' . $item_sort . '</th></tr>';

  $rowclass = begin_row_class();

  foreach($item_list as $item)
  {
    $donators = get_museum_item_count($item['itemid']);
  
    echo '<tr class="' . $rowclass . '"><td class="centered">' . item_display($item, '') . '</td><td>' . $item['itemname'] . '</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
