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

$resident = get_user_bydisplay($_GET['resident'], 'display,idnum');

if($resident === false)
{
  header('Location: /directory.php');
  exit();
}

$options = array();

$item_count = get_user_museum_count($resident['idnum']);

if($item_count >= 100)
{
  $page = (int)$_GET['page'];
  $max_pages = ceil($item_count / 20);

  if($page < 1 || $page > $max_pages)
    $page = 1;

  $item_list = get_user_museum_page($resident['idnum'], $page);

  $pages = paginate($max_pages, $page, '/museum/view.php?resident=' . $resident['display'] . '&page=%s');
  
  $dialog_text = '<p>This is a collection of the ' . $item_count . ' items that ' . resident_link($resident['display']) . ' has generously donated.';

  $command = 'SELECT idnum,name FROM psypets_museum_displays WHERE userid=' . $resident['idnum'];
  $displays = fetch_multiple($command, 'fetching resident\'s displays');

  if(count($displays) > 0)
  {
    $word = (count($displays) == 1 ? 'a Display' : 'a couple Displays');
  
    $dialog_text .= '</p><p>' . resident_link($resident['display']) . ' has also created ' . $word . ':</p><p><ul>';

    foreach($displays as $display)
      $dialog_text .= '<li><a href="/museum/viewdisplay.php?id=' . $display['idnum'] . '">' . $display['name'] . '</a></li>';
    
    $dialog_text .= '</ul></p><p>Enjoy!</p>';
  }
  else
    $dialog_text .= '  Enjoy!</p>';
}
else
  $dialog_text = '<p>' . resident_link($resident['display']) . ' has not donated enough items to get a wing of their own.</p>';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; <?= $resident['display'] ?>'s Wing</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/museum/">The Museum</a> &gt; <?= $resident['display'] ?>'s Wing</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/museum/view.php?resident=<?= urlencode($resident['display']) ?>"><?= $resident['display'] ?>'s Collection</a></li>
      <li><a href="/museum/uncollection.php?resident=<?= urlencode($resident['display']) ?>"><?= $resident['display'] ?>'s Uncollection</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if($item_count >= 100)
{
  echo $pages .
       '<table>' .
       '<tr class="titlerow"><th></th><th>Item</th><th class="centered">Donated</th><th>Donators</th></tr>';

  $rowclass = begin_row_class();

  foreach($item_list as $item)
  {
    $donators = get_museum_item_count($item['itemid']);
  
    echo '<tr class="' . $rowclass . '"><td class="centered">' . item_display($item, '') . '</td><td>' . $item['itemname'] . '</td><td class="centered">' . Duration($now - $item['timestamp'], 2) . ' ago</td><td class="centered"><a href="/museum/donators.php?item=' . $item['itemid'] . '">' . $donators . '</a></td></tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       $pages;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
