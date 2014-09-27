<?php
if($okay_to_be_here !== true)
  exit();

require_once 'commons/petlib.php';
require_once 'commons/adventurelib.php';

$challenge_tokens = get_challenge_tokens($user['idnum']);
if($challenge_tokens === false)
{
  create_challenge_tokens($user['idnum']);
  $challenge_tokens = get_challenge_tokens($user['idnum']);
  if($challenge_tokens === false)
    die('error loading and/or creating adventure tokens.  this is bad.');
}

if($_POST['petid'] > 0)
  $target_pet = get_pet_byid((int)$_POST['petid']);
else
  $target_pet = array();

if($target_pet['user'] != $user['user'] || $target_pet['dead'] != 'no' || $target_pet['changed'] == 'yes' || $target_pet['sleeping'] == 'yes' || $target_pet['zombie'] == 'yes')
{
?>
<p>Which pet will venture into the <?= $this_inventory['itemname'] ?>?  <i>(Dead, sleeping, and wereform pets may not be asked to enter.)</i></p>

<?php
  foreach($userpets as $pet)
  {
    if($pet['dead'] == 'no' && $pet['zombie'] == 'no' && $pet['changed'] == 'no' && $pet['sleeping'] == 'no')
      $options[] = '<option value="' . $pet['idnum'] . '">' . $pet['petname'] . '</option>';
  }
  
  if(count($options) > 0)
    echo '
      <form method="post">
      <p><select name="petid">' . implode('', $options) . '</select> <input type="submit" name="submit" value="Enter" /></p>
      </form>
    ';
  else
    echo '<p class="failure">No pets are available.</p>';
}
else
{
  delete_inventory_byid($this_inventory['idnum']);

  echo '<p>' . $target_pet['petname'] . ' enters the door, returning moments later with ';

  if($target_pet['int'] + $target_pet['wit'] > 20)
    $itemname = 'Golden Mushroom';
  else if($target_pet['int'] + $target_pet['wit'] > 15)
    $itemname = 'Poisonous Mushroom';
  else if($target_pet['int'] + $target_pet['wit'] > 10)
    $itemname = 'Chanterelle';
  else if($target_pet['int'] + $target_pet['wit'] > 5)
    $itemname = 'Mushroom';
  else
    $itemname = '';
  
  if($itemname != '')
  {
    $prizes[] = 'a ' . $itemname;
    add_inventory($user['user'], '', $itemname, 'Found in a ' . $this_inventory['itemname'], $this_inventory['location']);
  }

  $coins = 1 + (int)log($target_pet['athletics'] + $target_pet['str'] + $target_pet['dex'], 2);

  for($x = 0; $x < $coins; ++$x)
  {
    $a = mt_rand(1, 100);
    if($a <= 30)
      $tokens['plastic']++;
    else if($a <= 60)
      $tokens['copper']++;
    else if($a <= 80)
      $tokens['silver']++;
    else if($a <= 95)
      $tokens['gold']++;
    else
      $tokens['platinum']++;
  }

  foreach($tokens as $type=>$qty)
  {
    $prizes[] = $qty . ' ' . ucfirst($type) . ' token' . ($qty == 1 ? '' : 's');
    $challenge_tokens[$type] += $qty;
  }

  update_challenge_tokens($challenge_tokens);

  echo list_nice($prizes) . '!</p><p>Mysteriously, and rather inconveniently, the door then vanishes.</p>';

  train_pet($target_pet, 'int', ($target_pet['int'] + 1) * 3, 0, false, true);
  train_pet($target_pet, 'wit', ($target_pet['wit'] + 1) * 3, 0, false, true);
  train_pet($target_pet, 'athletics', ($target_pet['athletics'] + 1) * 2, 0, false, true);
  train_pet($target_pet, 'str', ($target_pet['str'] + 1) * 2, 0, false, true);
  train_pet($target_pet, 'dex', ($target_pet['dex'] + 1) * 2, 0, false, true);

  $AGAIN_WITH_ANOTHER = true;
}
?>
