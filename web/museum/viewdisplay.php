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

$id = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_museum_displays WHERE idnum=' . $id . ' LIMIT 1';
$display = fetch_single($command, 'fetching display');

if($display === false)
{
  header('Location: /museum/');
  exit();
}

if($display['num_items'] > 0)
  $items = explode(';', $display['items']);
else
  $items = array();

$owner = get_user_byid($display['userid']);

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; <?= $owner['display'] ?> &gt; <?= $display['name'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/museum/">The Museum</a> &gt; <a href="/museum/view.php?resident=<?= link_safe($owner['display']) ?>"><?= $owner['display'] ?></a> &gt; <?= $display['name'] ?></h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/museum/view.php?resident=<?= urlencode($owner['display']) ?>"><?= $owner['display'] ?>'s Collection</a></li>
      <li><a href="/museum/uncollection.php?resident=<?= urlencode($owner['display']) ?>"><?= $owner['display'] ?>'s Uncollection</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<ul><li><a href="/museum/view.php?resident=<?= link_safe($owner['display']) ?>">Back to <?= $owner['display'] ?>'s Wing</a></li></ul>
<?php
if($display['num_items'] > 0)
{
  $rowclass = begin_row_class();

  echo '<table><thead><tr class="titlerow"><th></th><th>Item</th></tr></thead><tbody>';

  foreach($items as $item)
  {
    $details = get_item_byname($item);

    echo '<tr class="' . $rowclass . '"><td class="centered">' . item_display($details, '') . '</td><td>' . $item . '</td></tr>';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '</tbody></table>' .
       '<ul><li><a href="/museum/view.php?resident=' . link_safe($owner['display']) . '">Back to ' . $owner['display'] . '\'s Wing</a></li></ul>';
}
else
  echo '<p>This Display contains no items! (What a rip!)</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
