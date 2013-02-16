<?php
$whereat = 'tailor';
$wiki = 'The_Tailory';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';

if($now_month != 10)
{
  header('Location: ./404.php');
  exit();
}

$message = array(
  '<p>Hi there!  I run this Tailory.  You might remember me from last year.  That was quite an event!</p>' .
  '<p>Anyway, please take a look at my designs.  I may come up with more before Halloween night, so be sure to check back now and again!</p>'
);

if($_GET['dialog'] == 'alien')
{
  $dialog = '<p>Oh, that?  The Alchemist gave me the money to pay for a bunch of the costumes, but said he doesn\'t actually want one himself... and rather, that if a Resident asks for one, I should offer to put it together for free by using the money he gave me.</p>' .
            '<p>You don\'t think there\'s going to be another alien attack, like last year, do you...?</p>';
}
else
{
  $dialog = $message[array_rand($message)];
  $options[] = '<a href="tailor.php?dialog=alien">Ask about the Alien Costume</a>';
}

$this_tailor = $database->FetchMultipleBy('SELECT * FROM monster_smith WHERE type=\'tailor\' ORDER BY cost ASC', 'idnum');

$inventory = $database->FetchMultipleBy('
	SELECT COUNT(idnum) AS qty,itemname
	FROM monster_inventory
	WHERE
		user=' . $database->Quote($user['user']) . '
		AND location=\'storage\'
	GROUP BY itemname
', 'itemname');

if($_POST['tailorid'] > 0)
{
  $tailor_recipe = $this_tailor[$_POST['tailorid']];
  if($tailor_recipe['idnum'] > 0)
  {
    $ingredients = explode(',', $tailor_recipe['supplies']);
    $itemcounts = array();
    foreach($ingredients as $item)
      $itemcounts[$item]++;

    $ok = true;

    $itemdescripts = array();
    foreach($itemcounts as $item=>$count)
    {
      if($inventory[$item]['qty'] < $count)
        $ok = false;
    }

    if(!$ok)
      $error_message = '"You don\'t seem to quite have all the materials I\'ll need."';

    if($user['money'] < $tailor_recipe['cost'])
      $ok = false;
    else
      $error_message = '"I\'m afraid I can\'t sell it for anything less than list price.  Sorry!"';

    if($ok)
    {
      $transaction_value = $tailor_recipe['cost'];

      foreach($itemcounts as $item=>$count)
      {
        delete_inventory_byname($user['user'], $item, $count, 'storage');

        $item_details = get_item_byname($item);
        $transaction_value += $item_details['value'] * $count;
      }

      $make_list = explode(',', $tailor_recipe['makes']);
      foreach($make_list as $item)
      {
        $message = 'This item was sewn at The Tailory.';

        add_inventory($user['user'], '', $item, $message, $user['incomingto']);
      }

      if($tailor_recipe['cost'] > 0)
        take_money($user, $tailor_recipe['cost'], 'Tailory fee');

      header('Location: ./tailor.php?msg=13:' . $user['incomingto']);
      exit();
    }
  }
  else
    $error_message = '"Sew what now?"';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Tailory</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Tailory</h4>
<?php
if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

include 'commons/dialog_open.php';

if($error_message)
  echo "<p>$error_message</p>";
else
  echo $dialog;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>' , $options) . '</li></ul>';
?>
     <form action="tailor.php" method="post">
     <table>
      <tr class="titlerow">
       <th></th>
       <th></th>
       <th>What&nbsp;to&nbsp;Sew</th>
       <th>Materials&nbsp;Needed</th>
       <th>Additional&nbsp;Cost</th>
      </tr>
<?php
$bgcolor = begin_row_class();

foreach($this_tailor as $tailor_recipe)
{
  $ingredients = explode(',', $tailor_recipe['supplies']);
  $itemcounts = array();
  foreach($ingredients as $item)
    $itemcounts[$item]++;

  $ok = true;
  $enough_money = true;

  $itemdescripts = array();
  foreach($itemcounts as $item=>$count)
  {
    if($inventory[$item]['qty'] < $count)
    {
      if($inventory[$item]['qty'] == 0)
        $inventory[$item]['qty'] = "0";

      $itemdescripts[] = '<a href="encyclopedia2.php?item=' . link_safe($item) . '" class="failure">' . $item . ($count > 1 ? ' (' . $inventory[$item]['qty'] . ' / ' . $count . ')' : '') . '</a>';
      $ok = false;
    }
    else
      $itemdescripts[] = '<a href="encyclopedia2.php?item=' . link_safe($item) . '">' . $item . ($count > 1 ? ' (' . $count . ')' : '') . '</a>';
  }

  if($user['money'] < $tailor_recipe['cost'])
    $enough_money = false;

  $supplies = implode('<br />', $itemdescripts);

  if($ok == false && $tailor_recipe['secret'] == 'yes')
    continue;

  $makes_item = get_item_byname($tailor_recipe['makes']);
?>
      <tr class="<?= $bgcolor ?>">
<?php
  if($ok && $enough_money)
  {
?>
       <td><input type="radio" name="tailorid" value="<?= $tailor_recipe['idnum'] ?>" /></td>
<?php
  }
  else
  {
?>
       <td><input type="radio" name="tailorid" value="0" disabled /></td>
<?php
  }
?>
       <td class="centered"><?= item_display($makes_item, '') ?></td>
       <td><?= $tailor_recipe['makes'] ?></td>
       <td valign="top"><?= $supplies ?></td>
<?php
  if($enough_money)
    echo '       <td valign="top" align="center">' . $tailor_recipe['cost'] . '<span class="money">m</span></td>';
  else
    echo '       <td valign="top" align="center"><span class="failure">' . $tailor_recipe['cost'] . '<span class="money">m</span></span></td>';
?>
      </tr>
<?php
  $bgcolor = alt_row_class($bgcolor);
}
?>
      <tr>
       <td colspan="4" align="center"><input type="submit" value="Sew Item" /></td>
      </tr>
     </table>
     </form>
     <p><i>(Costumes do not affect pet stats, however, only pets equipped with costumes will be able to participate in Halloween Trick-or-Treating.)</i></p>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
