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
  header('Location: ./groupindex.php');
  exit();
}

$ranks = get_group_ranks($groupid);
$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
  $invites = get_invites_bygroup($groupid);
  $rankid = get_member_rank($group, $user['idnum']);
  $can_manage_money = (rank_has_right($ranks, $rankid, 'treasurer') || $group['leaderid'] == $user['idnum']);
}
else
  $can_manage_money = false;

if(!$can_manage_money)
{
  header('Location: ./grouppage.php');
  exit();
}

$command = 'SELECT * FROM psypets_group_currencies WHERE groupid=' . $groupid . ' AND `type`=\'pet\'';
$pet_currencies = $database->FetchMultiple($command, 'fetching group currencies');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Currencies &gt; Pet Wealth</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Currencies &gt; Pet Wealth</h4>
<?php
$activetab = 'groupcurrency';
include 'commons/grouptabs.php';

echo '
  <ul class="tabbed">
   <li><a href="groupcurrency_residents.php?id=' . $groupid . '">Resident Wealth</a></li>
   <li class="activetab"><a href="groupcurrency_pets.php?id=' . $groupid . '">Pet Wealth</a></li>
   <li><a href="groupcurrency_new.php?id=' . $groupid . '">Create New Currency</a></li>
  </ul>
';

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if(count($pet_currencies) > 0)
{
  echo '
    <h5>Manage Pet Wealth</h5>
    <form action="groupcurrency_pets.php?id=' . $groupid . '" method="post">
    <table>
     <tr class="titlerow">
      <th>Pet</th>
  ';

  $currency_ids = array();

  foreach($pet_currencies as $currency)
  {
    echo '<th><abbr title="' . $currency['name'] . '">' . $currency['symbol'] . '</abbr></th>';
    $currency_ids[] = $currency['idnum'];
  }

  echo '</tr>';

  $command = '
    SELECT a.petid,b.petname
    FROM
      psypets_group_pet_currencies AS a
      LEFT JOIN monster_pets AS b
    ON a.petid=b.idnum
    WHERE a.currencyid IN (' . implode(',', $currency_ids) . ')
    GROUP BY a.userid
  ';

  $pets = $database->FetchMultiple($command, 'fetching pets with wealth');

  $rowclass = begin_row_class();

  foreach($pets as $pet)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td>' . $pet['petname'] . '</td>
    ';

    $command = '
      SELECT currencyid,amount
      FROM psypets_group_pet_currencies
      WHERE petid=' . $pet['petid'];
    $wealth = $database->FetchMultipleBy($command, 'currencyid', 'fetching wealth of pet');

    foreach($pet_currencies as $currency)
      echo '<td class="righted">' . $wealth[$currency['idnum']]['amount'] . '</td>';

    echo '</tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     <tr class="' . $rowclass . '">
      <td><input type="text" name="pet" maxlength="24" /></td>
  ';

  foreach($pet_currencies as $currency)
    echo '<td class="righted"><input type="text" name="c' . $currency['idnum'] . '" size="3" /></td>';

  echo '
      <td><input type="submit" name="action" value="Add" /></td>
     </tr>
    </table>
    </form>
  ';
}
else
  echo '<p>' . $group['name'] . ' does not manage any pet currencies.</p><ul><li><a href="//' . $SETTINGS['site_domain'] . '/groupcurrency_new.php?id=' . $groupid . '">Create a new currency?</a></li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
