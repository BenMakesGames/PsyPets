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

$item_count = get_user_museum_count($user['idnum']);

if($item_count < 100)
{
  $dialog_text = '<p>Once you\'ve donated 100 items, and had a Wing devoted to your contributions, you\'ll be able to create Displays.</p>' .
                 '<p>Displays allow you to showcase a collection of items, hand-picked by you!  You could make a Display for all of your books, or foods, or all your green items... or just a hodgepodge of your favorite items - whatever you like!</p>';
}
else
{
  $dialog_text = '<p>You can create and edit Displays from here.</p>' .
                 '<p>Displays allow you to showcase a collection of items, hand-picked by you!  You could make a Display for all of your books, or foods, or all your green items... or just a hodgepodge of your favorite items - whatever you like!</p>' .
                 '<p>You can even showcase the same item in multiple Displays, so feel free to create as many Displays as you need.  (Though actually, for sanity\'s sake, we ask that you do not create more than 20 Displays.)</p>';

  $displays = fetch_multiple('
    SELECT idnum,name,num_items
    FROM psypets_museum_displays
    WHERE userid=' . $user['idnum'] . '
  ');
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Museum &gt; My Displays</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Museum &gt; My Displays</h4>
     <ul class="tabbed">
      <li><a href="/museum/">My Collection</a></li>
      <li><a href="/museum/uncollection.php">My Uncollection</a></li>
      <li><a href="/museum/donate.php">Make Donation</a></li>
      <li><a href="/museum/exchange.php">Exchanges</a></li>
      <li class="activetab"><a href="/museum/displayeditor.php">My Displays</a></li>
      <li><a href="/museum/wings.php">Wing Directory</a></li>
     </ul>
<?php
echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/museum.png" align="right" width="350" height="500" alt="(Museum Curator)" />';

include 'commons/dialog_open.php';

echo $dialog_text;

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if($item_count < 100)
  echo '<p>You must have a Wing before you can create any Displays.</p>';
else
{
  if(count($displays) == 0)
    echo '<p>You have not created any Displays.</p>';

  if(count($displays) < 20)
  {
?>
<form action="/museum/newdisplay.php" method="post">
<p><b>Name:</b> <input name="name" maxlength="30" size="30" /> <input type="submit" value="Create Display" class="bigbutton" /></p>
</form>
<?php
  }

  if(count($displays) > 0)
  {
?>
<table>
 <thead><tr class="titlerow">
  <th></th><th>Name</th><th>Items</th>
 </thead>
 <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($displays as $display)
    {
      echo '<tr class="' . $rowclass . '"><td><a href="/museum/editdisplay.php?id=' . $display['idnum'] . '">edit</a>, <a href="/museum/deletedisplay.php?id=' . $display['idnum'] . '" onclick="return confirm(\'It won\\\'t delete the items, but it might delete a lot of work you did.\r\nReally delete this Display?\');">delete</a></td>' .
           '<td>' . $display['name'] . '</td><td class="centered">' . $display['num_items'] . '</td></tr>';

      $rowclass = alt_row_class($rowclass);
    }
?>
 </tbody>
</table>
<?php
  }
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
