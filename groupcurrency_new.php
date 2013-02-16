<?php
$child_safe = false;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/formatting.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/checkpet.php';
require_once 'commons/userlib.php';
require_once 'commons/utility.php';
require_once 'commons/grouplib.php';

$groupid = (int)$_GET['id'];
$group = get_group_byid($groupid);

if($group === false)
{
  header('Location: ./directory.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($group['leaderid'] != $user['idnum'])
{
  header('Location: ./grouppage.php');
  exit();
}

$max_currencies = get_group_max_currencies($group);
$cur_currencies = get_group_cur_currencies($groupid);

if($_POST['action'] == 'Create Currency' && $cur_currencies < $max_currencies)
{
  $add_name = trim($_POST['name']);
  $add_symbol = trim($_POST['abbrev']);
  $add_use = trim($_POST['use']);
  
  if(strlen($add_name) < 1)
    $currency_errors[] = 'You forgot to enter a name!';
  else if(strlen($add_name) > 20)
    $currency_errors[] = 'Names are limited to 20 characters.';

  if(strlen($add_symbol) < 1)
    $currency_errors[] = 'You forgot to enter an abbreviation!';
  else if(strlen($add_symbol) > 3)
    $currency_errors[] = 'Abbreviations are limited to 3 characters.';
  else if(strtolower($add_symbol) == 'm')
    $currency_errors[] = 'Abbreviation may not be "m".  It\'s too confusing.';

  if($add_use != 'pet' && $add_use != 'resident')
    $currency_errors[] = 'Is this something given to pets, or to Residents?';

  if(count($currency_errors) == 0)
  {
    $command = '
      INSERT INTO psypets_group_currencies
      (groupid, type, name, symbol)
      VALUES
      (
        ' . $groupid . ',
        ' . quote_smart($add_use) . ',
        ' . quote_smart($add_name) . ',
        ' . quote_smart($add_symbol) . '
      )
    ';
    $database->FetchNone($command, 'adding currency');
  
    $add_name = '';
    $add_symbol = '';
    $add_use = '';
    
    $errors[] = '<span class="success">The new currency has been created!</span>';
    $cur_currencies++;
  }
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Currencies</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Currencies</h4>
<?php
$activetab = 'groupcurrency';
include 'commons/grouptabs.php';

echo '
  <ul class="tabbed">
   <li><a href="groupcurrency_residents.php?id=' . $groupid . '">Resident Wealth</a></li>
   <li><a href="groupcurrency_pets.php?id=' . $groupid . '">Pet Wealth</a></li>
   <li class="activetab"><a href="groupcurrency_new.php?id=' . $groupid . '">Create New Currency</a></li>
  </ul>
';

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

echo '<p>' . $group['name'] . ' can manage up to ' . $max_currencies . ' currencies.  So far, ' . $cur_currencies . ' ' . ($cur_currencies == 1 ? 'has' : 'have') . ' been defined.</p>';

if($cur_currencies < $max_currencies)
{
  if(count($currency_errors) > 0)
    echo '<ul><li class="failure">' . implode('</li><li class="failure">', $currency_errors) . '</li></ul>';
  else
    echo '<p>Remember, "currency" isn\'t limited to just money!  It could be used to track experience points, hit points, accomplishments, rank, or any other countable quality.</p><p>Take care when creating new currencies:  for the time being, they cannot be changed or deleted once created.</p>';
?>
<form action="groupcurrency_new.php?id=<?= $groupid ?>" method="post">
<table>
 <tr>
  <th valign="top">Use:</th>
  <td>
   <input type="radio" name="use" value="pet" disabled="disabled" /> <span class="dim">Given to pets (unimplemented)</span><br />
   <input type="radio" name="use" value="resident" /> Given to Residents<br />
  </td>
 </tr>
 <tr>
  <th>Name:</th>
  <td><input type="text" name="name" maxlength="20" value="<?= htmlentities($add_name) ?>" /></td>
  <td>(ex: "Awesome Bucks", "Experience Points", "Wishes"...) </td>
 </tr>
 <tr>
  <th>Abbreviation:</th>
  <td><input type="text" name="abbrev" maxlength="3" size="3" value="<?= htmlentities($add_symbol) ?>" /></td>
  <td>(shown on players' status bars, next to moneys, rupees, etc; may not be "m")</td>
 </tr>
</table>
<p><input type="submit" name="action" value="Create Currency" class="bigbutton" /></p>
</form>
<?php
}
else
  echo '<p>No more custom currencies can be created.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
