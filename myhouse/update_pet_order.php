<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/petlib.php';

foreach($_POST as $key=>$value)
{
  if($value == 'pet')
    $list[] = (int)substr($key, 1);
}

$i = 1;
foreach($list as $id)
{
  $this_pet = get_pet_byid($id);
  if($this_pet === false || $this_pet['user'] != $user['user'])
  {
    header('Location: /myhouse/arrange_pets.php');
    exit();
  }

  $new_orders[$id] = $i;
  $i++;
}

foreach($new_orders as $petid=>$orderid)
{
  fetch_none('
    UPDATE monster_pets
    SET orderid=' . $orderid . '
    WHERE idnum=' . $petid . '
    LIMIT 1
  ');
}

header('Location: /myhouse.php');
?>
