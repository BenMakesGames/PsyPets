<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/arklib.php';
require_once 'commons/questlib.php';

if($user['show_ark'] != 'yes')
{
  header('Location: /404/');
  exit();
}

$options = array();

$item_count = get_collection_count();

if($item_count > 0)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $collection_list = get_collection_page($page);

  $pages = paginate($max_pages, $page, 'ark_collections.php?page=%s');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Ark &gt; Collections</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Ark &gt; Collections</h4>
     <ul class="tabbed">
      <li><a href="ark.php">My Collection</a></li>
      <li><a href="ark_uncollection.php">My Uncollection</a></li>
      <li><a href="ark_donate.php">Make Donation</a></li>
      <li class="activetab"><a href="ark_collections.php">Collections</a></li>
     </ul>
<?php
//echo '<img src="gfx/npcs/flowergirl.jpg" align="right" width="350" height="706" alt="(Vanessa the Florist)" />';

include 'commons/dialog_open.php';

echo '<p>Ah, curious to see other Residents\' contributions?</p><p>What?  Why so many collections?  I told you, the chromosome combinations for pets are very strange!  And breeding can be very unreliable, as I\'m sure you know!  Mutations might also occur... I require redundancy to ensure accuracy!  This requires thoroughness if the puzzle is to be correctly solved!</p><p>At any rate, I wouldn\'t put the entire burden of collection on you!  No, no!  That would be ridiculous!  Even if you could manage, it\'d take far too long!</p>';

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($item_count == 0)
  echo '<p>There are no collections!  Not even one!  How unlikely!</p>';
else
{
  echo $pages .
       '<p><i>(Click the magnifying glass to view their collection.)</i></p>' .
       '<table>' .
       '<tr class="titlerow"><th></th><th>Resident</th><th>Collection</th></tr>';

  $rowclass = begin_row_class();

  foreach($collection_list as $collection)
  {
    echo '<tr class="' . $rowclass . '"><td><a href="ark_view.php?resident=' . link_safe($collection['display']) . '"><img src="gfx/search.gif" width="16" height="16" alt="view ark" /></a></td><td>' . resident_link($collection['display']) . '</td><td class="centered">' . $collection['arkcount'] . ' pets</td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
