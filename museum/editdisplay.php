<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/museumlib.php';

$id = (int)$_GET['id'];

$command = 'SELECT * FROM psypets_museum_displays WHERE idnum=' . $id . ' AND userid=' . $user['idnum'] . ' LIMIT 1';
$display = fetch_single($command, 'fetching display');

if($display === false)
{
  header('Location: /museum/displayeditor.php');
  exit();
}

if($display['num_items'] > 0)
  $items = explode(';', $display['items']);
else
  $items = array();

if(array_key_exists('delete', $_GET))
{
  $_POST['action'] = 'Remove';
  $_POST['item'] = urldecode($_GET['delete']);
}

if($_POST['action'] == 'Add')
{
  $item = trim($_POST['item']);

  $details = get_item_byname($item);

  if($details !== false)
  {
    $item = $details['itemname'];
  
    $command = 'SELECT itemid FROM psypets_museum WHERE itemid=' . $details['idnum'] . ' AND userid=' . $user['idnum'] . ' LIMIT 1';
    $owned = fetch_single($command, 'fetching owned museum item');
    
    if($owned !== false)
    {
      $i = array_search($item, $items);

      if($i !== false)
        $message = '<p class="failure">"' . $item . '" is already in this Display.</p>';
      else
      {
        $items[] = $item;
        $display['num_items']++;

        sort($items);

        $command = 'UPDATE psypets_museum_displays SET num_items=num_items+1,items=' . quote_smart(implode(';', $items)) . ' WHERE idnum=' . $id . ' LIMIT 1';
        fetch_none($command, 'updating display');
      }
    }
    else
      $message = '<p class="failure">You have not donated <a href="encyclopedia2.php?item=' . link_safe($item) . '">' . $item . '</a> to the Museum yet.</p>';
  }
  else
    $message = '<p class="failure">There is no item in all of ' . $SETTINGS['site_name'] . ' called "' . $item . '".</p>';
}
else if($_POST['action'] == 'Remove')
{
  $item = trim($_POST['item']);
  
  $i = array_search($item, $items);
  
  if($i === false)
    $message = '<p class="failure">There is no "' . $item . '" in this Display.</p>';
  else
  {
    unset($items[$i]);
    $display['num_items']--;

    $command = 'UPDATE psypets_museum_displays SET num_items=num_items-1,items=' . quote_smart(implode(';', $items)) . ' WHERE idnum=' . $id . ' LIMIT 1';
    fetch_none($command, 'updating display');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; My Displays &gt; <?= $display['name'] ?></title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Museum &gt; My Displays &gt; <?= $display['name'] ?></h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li><a href="/museum/donate.php">Make Donation</a></li>
      <li><a href="/museum/exchange.php">Exchanges</a></li>
      <li class="activetab"><a href="/museum/displayeditor.php">My Displays</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<ul><li><a href="/museum/displayeditor.php">Back to My Displays</a></li></ul>
<?php
if($display['num_items'] == 0)
  echo '<p>There are no items in this Display.</p>';

if($message != '')
  echo $message;
?>
<form action="/museum/editdisplay.php?id=<?= $id ?>" method="post">
<p><b>Item name:</b> <input name="item" maxlength="64" size="30" /> <input type="submit" name="action" value="Add" /> <input type="submit" name="action" value="Remove" /></p>
</form>
<?php
if($display['num_items'] > 0)
{
  $rowclass = begin_row_class();

  echo '<table><thead><tr class="titlerow"><th></th><th></th><th>Item</th></tr></thead><tbody>';

  foreach($items as $item)
  {
    $details = get_item_byname($item);

    echo '<tr class="' . $rowclass . '"><td><a href="/museum/editdisplay.php?id=' . $id . '&delete=' . urlencode($item) . '" alt="remove"><b class="failure">X</b></a></td><td class="centered">' . item_display($details, '') . '</td><td>' . $item . '</td></tr>';
    
    $rowclass = alt_row_class($rowclass);
  }

  echo '</tbody></table>';
}
?>
<ul><li><a href="/museum/displayeditor.php">Back to My Displays</a></li></ul>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
