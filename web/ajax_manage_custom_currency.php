<?php
$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/grouplib.php';
require_once 'commons/userlib.php';

$resident = $_POST['resident'];
$currency_id = (int)$_POST['currency'];
$action = $_POST['action'];
$amount = (int)$_POST['amount'];
$field_id = (int)$_POST['fieldid'];

if($amount == 0 || ($action != 'give' && $action != 'take'))
  die('Err0');

$command = '
  SELECT groupid,name
  FROM psypets_group_currencies
  WHERE
    idnum=' . $currency_id . '
    AND `type`=\'resident\'
  LIMIT 1
';
$currency_data = $database->FetchSingle($command, 'fetching currency data');

if($currency_data === false)
  die('Err1');

$target_user = get_user_bydisplay($resident, 'idnum,display');

if($target_user === false)
  die('Err2');

$groupid = $currency_data['groupid'];
$group = get_group_byid($groupid);

if($group === false)
  die('Err3');

$ranks = get_group_ranks($groupid);
/*$members = explode(',', $group['members']);
$organizer = get_user_byid($group['leaderid'], 'idnum,display,graphic');*/

$a_member = is_a_member($group, $user['idnum']);

if($a_member)
{
//  $invites = get_invites_bygroup($groupid);
  $rankid = get_member_rank($group, $user['idnum']);
  $can_manage_money = (rank_has_right($ranks, $rankid, 'treasurer') || $group['leaderid'] == $user['idnum']);
}
else
  $can_manage_money = false;

if(!$can_manage_money)
  die('Err4');

if($action == 'give')
{
  $command = '
    UPDATE psypets_group_player_currencies
    SET amount=amount+' . $amount . '
    WHERE
      userid=' . $target_user['idnum'] . '
      AND currencyid=' . $currency_id . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'updating player currency');
  
  if($database->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_group_player_currencies
      (userid, currencyid, amount)
      VALUES
      (
        ' . $target_user['idnum'] . ',
        ' . $currency_id . ',
        ' . $amount . '
      )
    ';
    $database->FetchNone($command, 'adding player currency');
  }
}
else if($action == 'take')
{
  $command = '
    UPDATE psypets_group_player_currencies
    SET amount=amount-' . $amount . '
    WHERE
      userid=' . $target_user['idnum'] . '
      AND currencyid=' . $currency_id . '
    LIMIT 1
  ';
  $database->FetchNone($command, 'updating player currency');

  if($database->AffectedRows() == 0)
  {
    $command = '
      INSERT INTO psypets_group_player_currencies
      (userid, currencyid, amount)
      VALUES
      (
        ' . $target_user['idnum'] . ',
        ' . $currency_id . ',
        -' . $amount . '
      )
    ';
    $database->FetchNone($command, 'adding player currency');
  }
}
else
  die('Log!');

$command = 'SELECT amount FROM psypets_group_player_currencies WHERE userid=' . $target_user['idnum'] . ' AND currencyid=' . $currency_id . ' LIMIT 1';
$new_value = $database->FetchSingle($command, 'fetching new currency value');

if($new_value === false)
  die('Err5');
  
$wealth_amount = $wealth_table_display = $new_value['amount'];

echo '<a href="#" onclick="add_remove_currency_for_resident(this, ' . $field_id . ', \'' . addslashes($target_user['display']) . '\', \'' . addslashes($currency_data['name']) . '\', ' . $currency_id . ', ' . $wealth_amount . '); return false;">' . $wealth_table_display . '</a>';
?>
