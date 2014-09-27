<?php
// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/inventory.php';
require_once 'commons/questlib.php';

if($NO_PVP)
{
  header('Location: ./lostdata.php');
  exit();
}

$given = get_quest_value($user['idnum'], 'gift value');

$items = array();
$keys = array();

foreach($_POST as $key=>$value)
{
  if(is_numeric($key))
  {
    if($value == "yes" || $value == "on")
      $keys[] = (int)$key;
  }
}

if(count($keys) > 0)
{
  // confirming that the player does own these items...
  $command = 'SELECT idnum FROM monster_inventory WHERE user=' . quote_smart($user['user']) . ' AND idnum IN (' . implode(',', $keys) . ') LIMIT ' . count($keys);
  $items = $database->FetchMultiple($command, 'givegift_a.php');
}
else
{
  header('Location: /givegift.php');
  exit();
}

if(count($items) > 0)
{
  $now = time();
  $yesterday = $now - 24 * 60 * 60;

  $users = $database->FetchMultiple('
    SELECT user
    FROM monster_users
    WHERE
      `user`!=' . quote_smart($user['user']) . '
      AND lastactivity>=' . $yesterday . '
      AND receive_giving_tree_gifts=\'yes\'
  ');

  if(count($users) == 0)
  {
    echo "No one to gift to?!<br />\n";
    exit();
  }

  $item_count = 0;
  $lupercalia = (date('M d') == 'Feb 15');
  $chance = 0;
  $total_value = (int)$given['value'];
  $recipients = array();

  if($_POST['credit'] == 'yes')
    $comment = 'This item was given to you by ' . $user['display'] . ' via the Giving Tree.';
  else
    $comment = 'This item was given to you via the Giving Tree.';

  foreach($items as $item)
  {
    $the_item = get_inventory_byid($item['idnum'], 'itemname,user');
    
    if($the_item['user'] != $user['user'])
      continue;
    
    $properties = get_item_byname($the_item['itemname']);

    if($properties['cursed'] == 'yes' || $properties['noexchange'] == 'yes')
      continue;

    if($lupercalia)
      $chance += $properties['ediblefood'];

    $item_count++;
    $total_value += $properties['value'];

    $destination = $users[array_rand($users)];

    if($the_item['itemname'] == 'The Importance of Being Earnest: Act I' && mt_rand(1, 2) == 1)
      $also = 'itemname=\'The Importance of Being Earnest: Act II\', ';
    else
      $also = '';

    $command = '
      UPDATE monster_inventory
      SET
        ' . $also . '
        `location`=\'storage/incoming\',
        forsale=0,
        `message2`=' . quote_smart($comment) . ',
        `user`=' . quote_smart($destination['user']) . '
      WHERE
        idnum=' . $item['idnum'] . '
      LIMIT 1
    ';

    $database->FetchNone($command, 'givegift_a.php');

    $recipients[$destination['user']] = true;
  }

  foreach($recipients as $username=>$value)
    flag_new_incoming_items($username);

  if($lupercalia && mt_rand(1, 1000) < $chance)
  {
    add_inventory($user['user'], 'gijubi', 'Fertility Draught', '', 'storage/incoming');
    flag_new_incoming_items($user['user']);
  }
   
  if($total_value > (int)$given['value'])
  {
    if($given === false)
      add_quest_value($user['idnum'], 'gift value', $total_value);
    else
      update_quest_value($given['idnum'], $total_value);

    $badges = get_badges_byuserid($user['idnum']);
    if($badges['giver'] == 'no' && $total_value > 10000)
    {
      $error_message[] = '90:Gift-Giver\'s';
      set_badge($user['idnum'], 'giver');
    }

    if($badges['giverplus'] == 'no' && $total_value > 20000)
    {
      $error_message[] = '90:Magnanimous Gift-Giver\'s';
      set_badge($user['idnum'], 'giverplus');
    }
  }
   
  $error_message[] = "60:$item_count";
}
else
{

}

if(count($error_message) > 0)
  $msg = '?msg=' . implode(',', $error_message);
else
  $msg = '';

header('Location: /givegift.php' . $msg);
?>
