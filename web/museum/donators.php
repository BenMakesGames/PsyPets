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

$options = array();

$itemid = (int)$_GET['item'];

$item = get_item_byid($itemid);

if($item === false || $item['custom'] != 'no')
{
  header('Location: /museum/');
  exit();
}

$num_donators = get_museum_item_count($itemid);

if($num_donators > 0)
{
  $max_pages = ceil($num_donators / 20);

  $page = (int)$_GET['page'];
  if($page < 1 || $page > $max_pages)
    $page = 1;

  $donators = get_museum_item_donators($itemid, $page);

  if($num_donators == 1)
    $dialog_text = '<p>Only one Resident has donated this item to us.</p>';
  else
    $dialog_text = '<p>' . $num_donators . ' Residents have donated this item to us.</p>';
  
  $dialog_text .= '<p>If any Resident has a magnifying glass next to their name, you may view the Museum Wing we have dedicated to that Resident\'s donations.  We only build Wings for Residents that have donated at least 100 items.</p>';
}
else
  $dialog_text = '<p>No one has donated one of these!  We could really use your help here!</p>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; <?= $item['itemname'] ?> Donators</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/museum/">The Museum</a> &gt; <?= $item['itemname'] ?> Donators</h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li><a href="/museum/donate.php">Make Donation</a></li>
      <li><a href="/museum/exchange.php">Exchanges</a></li>
      <li><a href="/museum/displayeditor.php">My Displays</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
      <li class="activetab"><a href="/museum/donators.php?item=<?= $itemid ?>"><?= $item['itemname'] ?> Donators</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if($num_donators > 0)
{
  $pages = paginate($max_pages, $page, '/museum/donators.php?item=' . $itemid . '&page=%s');
  
  echo $pages .
       '<table><tr class="titlerow">' .
       '<th></th><th>Resident</th><th class="centered">Donated</th></tr>';

  $rowclass = begin_row_class();

  foreach($donators as $donator)
  {
    $donation_count = $donator['museumcount'];
  
    echo '<tr class="' . $rowclass . '"><td>';

    if($donation_count >= 100)
      echo '<a href="/museum/view.php?resident=' . link_safe($donator['display']) . '"><img src="/gfx/search.gif" alt="(view wing)" width="16" height="16" /></a>';

    echo '</td><td>' . resident_link($donator['display']) . '</td><td class="centered">' . Duration($now - $donator['timestamp']) . ' ago</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' . 
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
