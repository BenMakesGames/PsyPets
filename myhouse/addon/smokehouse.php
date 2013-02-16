<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$whereat = 'home';
$wiki = 'Smokehouse';
$THIS_ROOM = 'Smokehouse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if(!addon_exists($house, 'Smokehouse'))
{
  header('Location: /myhouse.php');
  exit();
}

$inventory = get_houseinventory_byuser_forpets($user['user']);

$steak = 0;
$fish = 0;
$pork = 0;

foreach($inventory as $i)
{
  if($i['itemname'] == 'Steak')
    $steak++;
  else if($i['itemname'] == 'Fish')
    $fish++;
  else if($i['itemname'] == 'Pork')
    $pork++;
}

$m_rooms = take_apart(',', $house['rooms']);

foreach($m_rooms as $room)
  $rooms['home/' . $room] = $room;

$message = '<p>You can cure Steak and Fish in the Smokehouse to produce jerky in large quantities.</p>';

if($_POST['action'] == 'cure')
{
  $amount_s = (int)$_POST['steak'];
  $amount_f = (int)$_POST['fish'];
  $amount_p = (int)$_POST['pork'];

  $room = $_POST['room'];
  
  if($room == 'home' || array_key_exists($room, $rooms))
  {
    if($amount_s > 0)
    {
      if($amount_s > $steak)
        $steak = $amount_s;

      $used = delete_inventory_fromhome($user['user'], 'Steak', $amount_s);

      $spicy_possibility = (mt_rand(1, 3) == 1);

      if($used > 0)
      {
        $steak -= $used;
        $made = $used * 4;
        for($i = 0; $i < $made; ++$i)
        {
          if(mt_rand(1, 3) == 1 && $spicy_possibility)
          {
            add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Spicy Jerky', 'Prepared in ' . $user['display'] . '\'s smokehouse', $room);
            $spicy_jerky++;
          }
          else
          {
            add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Jerky', 'Prepared in ' . $user['display'] . '\'s smokehouse', $room);
            $jerky++;
          }
        }

        process_cached_inventory();

        $message .= '<p class="success">' . $used . ' Steak ' . ($used == 1 ? 'was' : 'were') . ' cured, yielding ';
        if($spicy_jerky > 0 && $jerky > 0)
          $message .= $jerky . ' Jerky and ' . $spicy_jerky . ' Spicy Jerky!  Where did this unexpected flavor come from?';
        else if($spicy_jerky > 0)
          $message .= $spicy_jerky . ' Spicy Jerky!  Where did this unexpected flavor come from?';
        else
          $message .= $jerky . ' Jerky.';
        $message .= '</p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Jerky Smoked', $made);
      }
    }

    if($amount_f > 0)
    {
      if($amount_f > $fish)
        $amount_f = $fish;

      $used = delete_inventory_fromhome($user['user'], 'Fish', $amount_f);

      if($used > 0)
      {
        $fish -= $used;
        $made = $used * 2;

        for($i = 0; $i < $made; ++$i)
          add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Smoked Fish', 'Prepared in ' . $user['display'] . '\'s smokehouse', $room);

        process_cached_inventory();

        $message .= '<p class="success">' . $used . ' Fish ' . ($used == 1 ? 'was' : 'were') . ' cured, yielding ';
        $message .= $made . ' Smoked Fish.';
        $message .= '</p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Fish Smoked', $made);
      }
    }

    if($amount_p > 0)
    {
      if($amount_p > $pork)
        $amount_p = $pork;

      $used = delete_inventory_fromhome($user['user'], 'Pork', $amount_p);

      if($used > 0)
      {
        $pork -= $used;
        $made = $used * 3;

        for($i = 0; $i < $made; ++$i)
          add_inventory_cached($user['user'], 'u:' . $user['idnum'], 'Bacon', 'Prepared in ' . $user['display'] . '\'s smokehouse', $room);

        process_cached_inventory();

        $message .= '<p class="success">' . $used . ' Pork ' . ($used == 1 ? 'was' : 'were') . ' cured, yielding ';
        $message .= $made . ' Bacon.';
        $message .= '</p>';

        require_once 'commons/statlib.php';
        record_stat($user['idnum'], 'Bacon Smoked', $made);
      }
    }
  }
  else
    $message .= '<p class="failure">Select a room to place the prepared items.</p>';
}

$room_list = '
  <select name="room">
   <option value="home">Common</option>
';

foreach($rooms as $room)
{
  if($room[0] == '$')
    $room_list .= '<option value="home/' . $room . '">' . ucfirst(substr($room, 1)) . '</option>';
  else
    $room_list .= '<option value="home/' . $room . '">' . ucfirst($room) . '</option>';
}

$room_list .= '
  </select>
';

require 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $user["display"] ?>'s House &gt; Smokehouse</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/myhouse.php"><?= $user['display'] ?>'s House</a> &gt; Smokehouse</h4>
<?php
echo $message;

room_display($house);

if($steak == 0 && $fish == 0 && $pork == 0)
  echo '<p>You do not have any smokeable meat in the house.</p>';
else
{
  $quantities = array();

  if($steak > 0)
    $quantities[] = $steak . ' Steak';
  if($fish > 0)
    $quantities[] = $fish . ' Fish';
  if($pork > 0)
    $quantities[] = $pork . ' Pork';
?>
<p>You have <?= list_nice($quantities) ?> in the house.  How many would you like to smoke?</p>
<form method="post">
<input type="hidden" name="action" value="cure" />
<table>
<?php
  if($steak > 0)
    echo '<tr><td><input name="steak" type="number" size="3" min="0" max="' . $steak . '" /> Steak</td></tr>';
  if($fish > 0)
    echo '<tr><td><input name="fish" type="number" size="3" min="0" max="' . $fish . '" /> Fish</td></tr>';
  if($pork > 0)
    echo '<tr><td><input name="pork" type="number" size="3" min="0" max="' . $pork . '" /> Pork</td></tr>';
?>
</table>
<p><input type="submit" value="Smoke" /> <?= $room_list ?></p>
<?php
} // have any steak/fish/pork
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
