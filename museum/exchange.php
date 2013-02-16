<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

$items_in = array(
  1 => array('Short Bone', 3),
  2 => array('Long Bone', 3),
);

$items_out = array(
  1 => 'Oddly-painted Rock',
  2 => 'Ornate Vase',
  3 => 'Large Rock',
  4 => 'Ruins',
);

$inventory = $database->FetchMultipleBy('
  SELECT COUNT(idnum) AS qty,itemname
  FROM monster_inventory
  WHERE
    `user`=' . quote_smart($user['user']) . '
    AND `location`=\'storage\'
  GROUP BY itemname
', 'itemname');

$errors = array();

if($_POST['submit'] == 'Exchange')
{
  $in = (int)$_POST['in'];
  $out = (int)$_POST['out'];
  $quantity = (int)$_POST['quantity'];
  
  $payment = $items_in[$in];

  if($quantity < 1)
    $errors[] = 'Sorry, how many?';
  else
  {
    if(!array_key_exists($in, $items_in) || $in <= 0)
      $errors[] = 'You <em>do</em> have bones, don\'t you?';
    else
    {
      if($inventory[$payment[0]]['qty'] < $payment[1] * $quantity)
        $errors[] = 'Mmmm... sorry, that\'s not quite enough bones...';
    }

    if(!array_key_exists($out, $items_out))
      $errors[] = 'Oh, no-no!  Please, allow me to compensate you for your efforts!';
  }

  if(count($errors) == 0)
  {
    delete_inventory_byname($user['user'], $payment[0], $payment[1] * $quantity, 'storage');
    
    $inventory[$payment[0]]['qty'] -= $payment[1] * $quantity;
    
    add_inventory_quantity($user['user'], '', $items_out[$out], $user['display'] . ' traded with the museum curator for this item', $user['incomingto'], $quantity);

    if($quantity > 1)
      $errors[] = 'Fantastic!  You\'ll find the ' . $quantity . ' ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '!';
    else
      $errors[] = 'Fantastic!  You\'ll find the ' . $items_out[$out] . ' in your ' . $user['incomingto'] . '!';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Museum</h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li><a href="/museum/donate.php">Make Donation</a></li>
      <li class="activetab"><a href="/museum/exchange.php">Exchanges</a></li>
      <li><a href="/museum/displayeditor.php">My Displays</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

if(count($errors) > 0)
  echo '<p>' . implode('</p><p>', $errors) . '</p>';
else
{
  echo '
    <p>Did I mention that we also work with Museums around the world in collecting bones?</p>
    <p>We do!</p>
    <p>If you happen to find any, I\'d gladly trade you for them!</p>
  ';
}

include 'commons/dialog_close.php';
?>
     <form method="post">
     <table class="nomargin">
      <tr>
       <td valign="top">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Bones</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_in as $id=>$in)
{
  $itemname = $in[0];
  $quantity = $in[1];

  echo '<tr class="' . $rowclass . '">';
  if($inventory[$itemname]['qty'] < $quantity)
    echo '<td><input type="radio" disabled /></td><td class="failure centered">';
  else
    echo '<td><input type="radio" name="in" value="' . $id . '" /></td><td class="centered">';

  echo (int)$inventory[$itemname]['qty'] . ' / ' . $quantity . '</td><td>' . item_text_link($itemname) . '</td>' .
       '</tr>';

  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
       </td>
       <td valign="top" style="padding-left: 2em;">
        <table>
         <tr class="titlerow"><th></th><th></th><th>Compensation</th></tr>
<?php
$rowclass = begin_row_class();

foreach($items_out as $id=>$potion)
{
  $details = get_item_byname($potion);
?>
         <tr class="<?= $rowclass ?>">
          <td><input type="radio" name="out" value="<?= $id ?>" /></td>
          <td class="centered"><?= item_display_extra($details) ?></td>
          <td><?= $potion ?></td>
         </tr>
<?php
  $rowclass = alt_row_class($rowclass);
}
?>
        </table>
       </td>
      </tr>
     </table>
     <p>Quantity: <input name="quantity" maxlength="3" size="3" value="1" /> <input type="submit" name="submit" value="Exchange" /></p>
     </form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
