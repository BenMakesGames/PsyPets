<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/utility.php';
require_once 'commons/itemlib.php';

if(strlen(trim($_GET['location'])) > 0)
  $location = trim($_GET['location']);
else
  $location = 'storage';

$wheres = array(
  'user=' . quote_smart($user['user']),
  'location=' . quote_smart($location),
);

if(strlen(trim($_GET['name-part'])) > 0)
  $wheres[] = 'itemname LIKE ' . quote_smart('%' . trim($_GET['name-part']) . '%');
  
if($_GET['grouped'])
  $command = '
    SELECT itemname,COUNT(idnum) AS quantity
    FROM monster_inventory
    WHERE ' . implode(' AND ', $wheres) . '
    GROUP BY itemname
  ';
else
  $command = '
    SELECT *
    FROM monster_inventory
    WHERE ' . implode(' AND ', $wheres) . '
  ';

$items = $database->FetchMultiple($command);

if(count($_GET['join']) > 0 && $_GET['grouped'])
{
  $join_graphics = in_array('graphics', $_GET['join']);

  foreach($items as &$item)
  {
    $details = get_item_byname($item['itemname']);

    if($join_graphics)
    {
      $item['graphic'] = $details['graphic'];
      $item['graphictype'] = $details['graphictype'];
      $item['itemtype'] = $details['itemtype'];
    }
  }
}

echo json_encode($items);
?>