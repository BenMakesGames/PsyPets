<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/houselib.php';
require_once 'commons/kitchenlib.php';

if(!addon_exists($house, 'Kitchen'))
{
  header('Location: /myhouse.php');
  exit();
}

$id = (int)$_POST['recipe'];

$known_recipe = $database->FetchSingle('SELECT * FROM psypets_known_recipes WHERE userid=' . (int)$user['idnum'] . ' AND recipeid=' . $id . ' LIMIT 1');

if($known_recipe !== false)
{
  $response = array('id' => $known_recipe['recipeid']);

  if($known_recipe['favorite'] == 'yes')
  {
    $favorite = 'no';
    $response['html'] = '&#9734;';
  }
  else
  {
    $favorite = 'yes';
    $response['html'] = '&#9733;';
  }
  
  $database->FetchNone('UPDATE psypets_known_recipes SET favorite=' . $database->Quote($favorite) . ' WHERE idnum=' . (int)$known_recipe['idnum'] . ' LIMIT 1');
}
else
{
  $response = array('error' => true);
}

echo json_encode($response);
?>