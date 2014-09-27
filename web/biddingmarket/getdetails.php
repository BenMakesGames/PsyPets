<?php
require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/itemlib.php';

$itemname = trim($_GET['itemname']);

$details = fetch_multiple('
  SELECT SUM(quantity) AS qty,bid
  FROM psypets_reversemarket
  WHERE itemname=' . quote_smart($itemname) . '
  GROUP BY bid
  ORDER BY bid DESC
');

echo json_encode($details);
?>