<?php
$whereat = 'petshelter';
$require_petload = 'yes'; // we really only NEED count($userpets)

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/itemlib.php';
require_once 'commons/economylib.php';

if($user['adoptedtoday'] == 'yes')
{
  header('Location: ./petshelter.php');
  exit();
}

$pet_cost = value_with_inflation(275);

if($user['money'] >= $pet_cost)
{
  $command = 'SELECT * FROM monster_pets WHERE idnum=' . quote_smart($_POST['petid']) . " AND last_check<=$now AND `user`='" . $SETTINGS['site_ingame_mailer'] . "' LIMIT 1";
  $this_pet = $database->FetchSingle($command, 'buying a pet');

  if($this_pet !== false)
  {
    if($this_pet['user'] != $SETTINGS['site_ingame_mailer'])
    {
      header('Location: ./petshelter.php?msg=25');
      exit();
    }
  }
  else
  {
    header('Location: ./petshelter.php');
    exit();
  }
}
else
{
  header('Location: ./petshelter.php?msg=22');
  exit();
}

if(substr($_POST['submit'], 0, 7) == 'Buy Pet')
{
  $_POST['petname'] = $this_pet['petname'];
}

else if($_POST['submit'] == 'Take Home')
{
  $_POST['petname'] = trim($_POST['petname']);

  if(strlen($_POST['petname']) > 32 || strlen($_POST['petname']) < 1)
    $petname_message = 'Your pet\'s name must be between 1 and 32 characters.';
  else
  {
    $item = get_inventory_byid($this_pet['toolid']);
    if($this_pet['toolid'] > 0 && $item === false)
      $toolid = 0;
    else
      $toolid = $this_pet['toolid'];

    $key = get_inventory_byid($this_pet['keyid']);
    if($this_pet['keyid'] > 0 && $key === false)
      $keyid = 0;
    else
      $keyid = $this_pet['keyid'];

    if(pet_level($this_pet) > 5)
      $original = 'no';
    else
      $original = 'yes';

    $command = 'UPDATE monster_pets ' .
               'SET user=' . quote_smart($user['user']) . ', location=\'home\', ' .
               'original=' . quote_smart($original) . ', ' .
               'petname=' . quote_smart($_POST['petname']) . ', ' .
               'toolid=' . $toolid . ', ' .
               'keyid='. $keyid . ', ' .
               'last_check=' . $now . ', ' .
               'last_love=' . ($now - 31 * 60) . ', ' .
               'orderid=' . count($userpets) . ', ' .
               'energy=12, food=12, safety=5, love=5, esteem=5, ' .
               'protected=\'no\', sleeping=\'no\', dead=\'no\', changed=\'no\', ' .
               'history=CONCAT(history, ' . quote_smart('<br />purchased from the pet shelter by ' . $user['display'] . ' at ' . $now) . ') ' .
               'WHERE idnum=' . $this_pet['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'buypet.php');

    // this matches the random pet purchase from petshelter.php
    take_money($user, $pet_cost, 'Bought a pet from the Pet Shelter');

    $command = 'UPDATE monster_users SET adoptedtoday=\'yes\' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'marking today\'s adoption');

    require_once 'commons/statlib.php';
    record_stat($user['idnum'], 'Bought a Pet at the Pet Shelter', 1);

    $badges = get_badges_byuserid($user['idnum']);
    if($badges['adopter'] == 'no')
    {
      set_badge($user['idnum'], 'adopter');

      psymail_user(
        $user['user'],
        'klittrell',
        'Thanks for giving ' . $_POST['petname'] . ' a new home!',
        'I just wanted to thank you for adopting one of the pets!  It may take ' . $_POST['petname'] . ' a little while to feel comfortable in its new home, but have patience.<br /><br />I\'m sure you\'ll raise a wonderful pet!<br /><br />{i}(You received the Pet Adopter badge!  Check it out on your profile!){/}'
      );
    }

    header('Location: /myhouse.php');
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Pet Shelter &gt; Adopt Pet</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <?= ($check_message ? "<p style=\"color:blue;\">$check_message</p>" : "") ?>
     <h4><a href="petshelter.php">Pet Shelter</a> &gt; Adopt Pet</h4>
     <table>
      <form action="buypet.php" method="post">
      <input type="hidden" name="petid" value="<?= $_POST['petid'] ?>">
<?php
$color = ($this_pet['gender'] == 'male' ? '#3333ff' : '#ff3333');

$description = 'Level ' . pet_level($this_pet) . ' pet';
?>
      <tr>
       <td><img src="gfx/pets/<?= $this_pet['graphic'] ?>"></td>
       <td><span style="color:<?= $color ?>;"><?= $this_pet['petname'] ?></span></td>
       <td><?= $description ?></td>
      </tr>
      <tr>
       <td colspan=3>
<?php
if($petname_message)
  echo "        <p>$petname_message</p>\n";
else
  echo "        <p>Please name this pet.</p>\n";
?>
        <input name="petname" maxlength="32" value="<?= $_POST['petname'] ?>">
       </td>
      </tr>
      <tr>
       <td colspan="3" align="center"><input type="submit" name="submit" value="Take Home"<?= ($user['money'] < $pet_cost ? ' disabled=yes' : '') ?> /></td>
      </tr>
      </form>
     </table>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
