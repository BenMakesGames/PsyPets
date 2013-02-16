<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($admin['manageitems'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$starttime = microtime();

$items_to_check = $database->FetchMultiple(('
  SELECT itemname,recycle_for
  FROM monster_items
  WHERE recycle_for!=\'\'
');

foreach($items_to_check as $this_item)
{
  $materials = array_unique(explode(',', $this_item['recycle_for']));
  
  foreach($materials as $mat)
  {
    $item = get_item_byname($mat);

    if(!is_array($item))
      $invalid_items[$this_item['itemname']][] = $mat;
  }
}

$duration = microtime() - $starttime;

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Item Materials Validator</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
  <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Item Materials Validator</h4>
<?php
echo '<p>Checked ' . count($items_to_check) . ' items in ' . round($duration / 1000, 4) . 's.</p>';

if(count($invalid_items) > 0)
{
  foreach($invalid_items as $itemname=>$mats)
    echo '<h5>' . $itemname . '</h5><ul><li>"' . implode('"</li><li>"', $mats) . '"</li></ul>';
}
else
  echo '<p>Congrats!  No items have invalid materials.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
