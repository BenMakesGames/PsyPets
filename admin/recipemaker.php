<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/utility.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$rows = array();

if($_POST['action'] == 'Advise')
{
  if(strpos($_POST['recipe'], ';') !== false)
    $items = take_apart(';', $_POST['recipe']);
  else
    $items = take_apart(',', $_POST['recipe']);

  foreach($items as $item)
  {
    $details = get_item_byname($item);

    if($details === false)
      $rows[] = array($item, '', '', 'An item by this name does not exist.');
    else
    {
      $total = $details['ediblefood'] + $details['ediblelove'];
      
      $notes = array();
      if($details['ediblesafety'] != 0)
        $notes[] = 'Safety: ' . $details['ediblesafety'];
      if($details['edibleesteem'] != 0)
        $notes[] = 'Esteem: ' . $details['edibleesteem'];
      if($details['edibleenergy'] != 0)
        $notes[] = 'Energy: ' . $details['edibleenergy'];
      if($details['ediblecaffeine'] != 0)
        $notes[] = 'Caffeine: ' . $details['ediblecaffeine'];
      if($details['admin_notes'] != '')
        $notes[] = $details['admin_notes'];

      $rows[] = array($item, $total, $details['value'], implode(', ', $notes));
    }
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Recipe Maker</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Recipe Maker</h4>
     <form method="post">
     <p><input type="text" name="recipe" value="<?= $_POST['recipe'] ?>" /> <input type="submit" name="action" value="Advise" />
     </form>
<?php
if(count($rows) > 0)
{
  echo '<hr /><table><thead><tr class="titlerow"><th>Item</th><th>Food/Love</th><th>Moneys</th><th>Notes</th></tr></thead><tbody>';

  $total = 0;
  $moneys = 0;

  foreach($rows as $row)
  {
    echo '<tr><th>' . item_text_link($row[0]) . '</th><td class="centered">' . $row[1] . '</td><td class="centered">' . $row[2] . '</td><td>' . $row[3] . '</td></tr>';
    $total += $row[1];
    $moneys += $row[2];
  }

  echo '<tr style="border-top: 1px solid #000;"><th>Total</th><td class="centered">' . $total . '</td><td class="centered">' . $moneys . '</td><td></td>';

  echo '</tbody></table>';
  echo '<p>Makes:</p><ol>';
  for($x = 1; $x <= 5; ++$x)
  {
    echo '<li>' . ceil($total / $x) . ' food/love and ' . ceil($moneys / $x) . ' moneys each</li>';
  }
  echo '</ol>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
