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

$command = 'SELECT * FROM psypets_group_currencies WHERE groupid=' . $groupid . ' AND `type`=\'resident\'';
$resident_currencies = $database->FetchMultiple($command, 'fetching group currencies');

if($_POST['action'] == 'Add')
{
  $resident = trim($_POST['resident']);

  foreach($resident_currencies as $currency)
  {
    $amount = (int)$_POST['c' . $currency['idnum']];
    if($amount > 0)
      $currencies[$currency['idnum']] = $amount;
  }

  $target_user = get_user_bydisplay($resident, 'idnum');
  
  if($target_user !== false)
  {
    foreach($currencies as $idnum=>$amount)
    {
      $command = '
        UPDATE psypets_group_player_currencies
        SET amount=amount+' . $amount . '
        WHERE
          userid=' . $target_user['idnum'] . '
          AND currencyid=' . $idnum . '
        LIMIT 1
      ';
      $database->FetchNone($command, 'updating resident currency');
      
      if($database->AffectedRows() == 0)
      {
        $command = '
          INSERT INTO psypets_group_player_currencies
          (userid, currencyid, amount)
          VALUES
          (
            ' . $target_user['idnum'] . ',
            ' . $idnum . ',
            ' . $amount . '
          )
        ';
        $database->FetchNone($command, 'inserting resident currency');
      }
    }
  }
  else
    $errors[] = '<span class="failure">There is no Resident by that name.</span>';
}

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; <?= $group['name'] ?> &gt; Currencies &gt; Resident Wealth</title>
  <script type="text/javascript" src="//<?= $SETTINGS['static_domain'] ?>/js/groupcurrency.js"></script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><?= $group['name'] ?>  &gt; Currencies &gt; Resident Wealth</h4>
<div id="currency_panel" style="display:none; width:200px; position:absolute; background-color:#fff; border:1px solid #000; padding:4px;"></div>
<?php
$activetab = 'groupcurrency';
include 'commons/grouptabs.php';

echo '
  <ul class="tabbed">
   <li class="activetab"><a href="groupcurrency_residents.php?id=' . $groupid . '">Resident Wealth</a></li>
   <li><a href="groupcurrency_pets.php?id=' . $groupid . '">Pet Wealth</a></li>
   <li><a href="groupcurrency_new.php?id=' . $groupid . '">Create New Currency</a></li>
  </ul>
';

if(count($errors) > 0)
  echo '<ul><li>' . implode('</li><li>', $errors) . '</li></ul>';

if(count($resident_currencies) > 0)
{
  $field_id = 1;

  echo '
    <h5>Manage Resident Wealth</h5>
    <p>Click on an amount to change that amount.</p>
    <form action="groupcurrency_residents.php?id=' . $groupid . '" method="post">
    <table>
     <tr class="titlerow">
      <th>Resident</th>
  ';

  $currency_ids = array();

  foreach($resident_currencies as $currency)
  {
    echo '<th class="righted"><abbr title="' . $currency['name'] . '">' . $currency['symbol'] . '</abbr></th>';
    $currency_ids[] = $currency['idnum'];
  }

  echo '</tr>';

  $command = '
    SELECT a.userid,b.display
    FROM
      psypets_group_player_currencies AS a
      LEFT JOIN monster_users AS b
    ON a.userid=b.idnum
    WHERE a.currencyid IN (' . implode(',', $currency_ids) . ')
    GROUP BY a.userid
  ';
  
  $residents = $database->FetchMultiple($command, 'fetching residents with wealth');
  
  $rowclass = begin_row_class();
  
  foreach($residents as $resident)
  {
    echo '
      <tr class="' . $rowclass . '">
       <td>' . resident_link($resident['display']) . '</td>
    ';
    
    $command = '
      SELECT currencyid,amount
      FROM psypets_group_player_currencies
      WHERE userid=' . $resident['userid'];
    $wealth = $database->FetchMultipleBy($command, 'currencyid', 'fetching wealth of resident');

    foreach($resident_currencies as $currency)
    {

      if(!array_key_exists($currency['idnum'], $wealth))
        $wealth_table_display = '&ndash;';
      else if($wealth[$currency['idnum']]['amount'] == 0)
        $wealth_table_display = '0';
      else
        $wealth_table_display = $wealth[$currency['idnum']]['amount'];

      $wealth_amount = (int)$wealth_table_display;

      echo '<td class="righted"><span id="field_' . $field_id . '"><a href="#" onclick="add_remove_currency_for_resident(this, ' . $field_id . ', \'' . addslashes($resident['display']) . '\', \'' . addslashes($currency['name']) . '\', ' . $currency['idnum'] . ', ' . $wealth_amount . '); return false;">' . $wealth_table_display . '</a></span></td>';

      $field_id++;
    }

    echo '</tr>';

    $rowclass = alt_row_class($rowclass);
  }

  echo '
     <tr class="' . $rowclass . '">
      <td><input type="text" name="resident" maxlength="24" /></td>
  ';

  foreach($resident_currencies as $currency)
    echo '<td class="righted"><input type="text" name="c' . $currency['idnum'] . '" size="3" /></td>';

  echo '
      <td><input type="submit" name="action" value="Add" /></td>
     </tr>
    </table>
    </form>
  ';
}
else
  echo '<p>' . $group['name'] . ' does not manage any Resident currencies.</p><ul><li><a href="/groupcurrency_new.php?id=' . $groupid . '">Create a new currency?</a></li></ul>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
