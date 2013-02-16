<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/itemlib.php';

$itemname = trim($_GET['itemname']);

$details = fetch_multiple('
  SELECT COUNT(a.idnum) AS qty,a.forsale,b.display
  FROM
    monster_inventory AS a
    LEFT JOIN monster_users AS b
      ON a.user=b.user
  WHERE
    a.itemname=' . quote_smart($itemname) . '
    AND b.openstore=\'yes\'
    AND a.forsale>0
  GROUP BY a.forsale,b.display
  ORDER BY a.forsale ASC,b.display ASC
');

echo json_encode($details);
?>
