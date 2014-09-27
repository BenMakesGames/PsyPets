<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/itemlib.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';

require_once 'commons/admincheck.php';

if($user['admin']['manageitems'] != 'yes')
{
  header('Location: /404/');
  exit();
}

if($_GET['action'] == 'recalc')
{
  $items = $database->FetchMultiple('
    SELECT itemname,idnum
    FROM monster_items
    WHERE custom=\'no\'
  ');

  $updated = 0;
  
  foreach($items as $item)
  {
    $database->FetchNone('
      UPDATE monster_items
      SET anagramname=\'' . alphabetize_letters($item['itemname']) . '\'
      WHERE idnum=' . $item['idnum'] . '
      LIMIT 1
    ');
    
    $updated += $database->AffectedRows();
  }
  
  $CONTENT['messages'][] = 'Done updating item anagram info.  ' . $updated . ' ' . ($updated == 1 ? 'item was' : 'items were') . ' updated.';
}

$items = $database->FetchMultiple('
  SELECT monster_items.itemname, monster_items.anagramname
  FROM monster_items
  INNER JOIN (
    SELECT anagramname
    FROM monster_items
    WHERE anagramname != \'\'
    GROUP BY anagramname
    HAVING COUNT(anagramname) > 1
  ) AS dup ON monster_items.anagramname = dup.anagramname
  ORDER BY monster_items.anagramname ASC
');

foreach($items as $item)
{
  $item_groups[$item['anagramname']][] = $item['itemname'];
}
require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Anagramizer</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Anagramizer</h4>
     <ul><li><a href="?action=recalc">Recalculate anagram info</a></li></ul>
<?php
foreach($item_groups as $group=>$items)
  echo '<h5>' . $group . '</h5><ul><li>' . implode('</li><li>', $items) . '</li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
