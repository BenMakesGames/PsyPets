<?php
if($okay_to_be_here !== true)
  exit();

list($money_type, $money_amount) = explode(',', $this_inventory['data']);

$money_amount = (int)$money_amount;

if($money_type == '' || $money_amount <= 0)
{
  $add_money = true;

  if($_POST['action'] == 'Seal Envelope')
  {
    $amount = (int)$_POST['amount'];
    
    if($amount < 1)
      echo '<p class="failure">You can\'t put in less than 1 money!</p>';
    else if($amount > $user['money'])
      echo '<p class="failure">You don\'t have that much money!</p>';
    else
    {
      take_money($user, $amount, 'Put into ' . $this_inventory['itemname'] . '.');
      
      $command = 'UPDATE monster_inventory SET data=\'moneys,' . $amount . '\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
      $database->FetchNone($command, 'sealing money in envelope');
      
      echo '
        <p>Done!</p>
        <script type="text/javascript">
        $(\'#moneysonhand\').html(\'' . ($user['money'] - $amount) . '\');
        </script>
      ';
      
      $add_money = false;
    }
  }

  if($add_money)
  {
    echo '
      <p>This ' . $this_inventory['itemname'] . ' is empty.  Would you like to put some money inside?</p>
      <form action="itemaction.php?idnum=' . $this_inventory['idnum'] . '" method="post">
      <table>
       <tr>
        <th>Moneys</th>
        <td><input type="text" name="amount" size="4" maxlength="' . strlen($user['money']) . '" /></td>
        <td><input type="submit" name="action" value="Seal Envelope" class="bigbutton" /></td>
       </tr>
      </table>
      </form>
    ';
  }

}
else if($_GET['action'] == 'open')
{
  switch($money_type)
  {
    case 'moneys':
      echo '<p>There\'s ' . $money_amount . '<span class="money">m</span> inside!</p>';
      give_money($user, $money_amount, 'Found in a ' . $this_inventory['itemname'] . '.');
      
      break;
    default:
      die('Bad item data!  Please report to an administrator, along with this item number: ' . $this_inventory['idnum'] . '.</p>');
  }

  echo '
    <p>You take it.</p>
    <script type="text/javascript">
    $(\'#moneysonhand\').html(\'' . ($user['money'] + $money_amount) . '\');
    </script>
  ';

  $command = 'UPDATE monster_inventory SET data=\'\' WHERE idnum=' . $this_inventory['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'retrieving money from envelope');
  
  $AGAIN_WITH_SAME = true;
}
else
{
  echo '
    <p>This sealed envelope seems to contain some money.  Do you want to open it?</p>
    <ul><li><a href="itemaction.php?idnum=' . $this_inventory['idnum'] . '&action=open">No time like the present!</a></li></ul>
  ';
}
?>
