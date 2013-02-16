<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';

$ok = false;

for($i = 0; $i < count($userpets); ++$i)
{
  if($userpets[$i]['idnum'] == (int)$_POST['pet'] && $userpets[$i]['dead'] == "no" && $userpets[$i]['sleeping'] == "yes")
  {
    $ok = true;
    $index = $i;
    break;
  }
}

if($ok == true)
{
  if($now - $userpets[$index]['last_love'] >= 30 * 60)
  {
    if($userpets[$index]['energy'] > 0)
    {
      $userpets[$index]['last_love'] = $now;
      $userpets[$index]['sleeping'] = "no";

      save_pet($userpets[$index], array('last_love', 'sleeping'));

      $msg = "65:" . $userpets[$index]['petname'];
    }
    else
      $msg = "66:" . $userpets[$index]['petname'];
  }
  else
    $msg = 29;
}

header('Location: ./myhouse.php?msg=' . $msg);
?>
