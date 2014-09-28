<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';
require_once 'commons/sqldumpfunc.php';

if($user['admin']['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if(array_key_exists('resident', $_GET))
{
  $resident = get_user_bydisplay($_GET['resident'], 'idnum,display,user');
  if($resident !== false)
  {
    $resident_name = $resident['display'];

    $command = 'SELECT * FROM monster_trades WHERE userid1=' . $resident['idnum'] . ' OR userid2=' . $resident['idnum'] . ' ORDER BY tradeid DESC';
    $trades = $database->FetchMultiple($command, 'fetching resident\'s trades');
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Track Trades</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Track Trades</h4>
     <form action="admintracktrades.php" method="get">
     <p>Resident: <input name="resident" value="<?= $resident_name ?>" /> <input type="submit" value="View" /></p>
     </form>
<?php
if($resident_name != '')
{
  echo '<hr />';

  if(count($trades) > 0)
  {
    $class = begin_row_class();

    echo '<table>' .
         '<tr class="titlerow"><th>Timestamp</th><th>Step</th><th>From</th><th></th><th></th><th>To</th><th></th><th>Dialog</th></tr>';
    foreach($trades as $trade)
    {
      $user1 = get_user_byid($trade['userid1'], 'idnum,display,user');
      $user2 = get_user_byid($trade['userid2'], 'idnum,display,user');
?>
<tr class="<?= $class ?>">
 <td><?= Duration($now - $trade['timestamp'], 2) ?> ago</td>
 <td class="centered"><?= $trade['step'] ?></td>
 <td><?= resident_link($user1['display']) ?><br />(<a href="/admin/resident.php?user=<?= $user1['user'] ?>"><?= $user1['user'] ?></a>)</td>
 <td><?= ($trade['money1'] != 0 ? $trade['money1'] . 'm<br />' : '') . $trade['itemsdesc1'] ?></td>
 <td> => </td>
 <td><?= resident_link($user2['display']) ?><br />(<a href="/admin/resident.php?user=<?= $user2['user'] ?>"><?= $user2['user'] ?></a>)</td>
 <td><?= ($trade['money2'] != 0 ? $trade['money2'] . 'm<br />' : '') . $trade['itemsdesc2'] ?></td>
 <td><?= $trade['dialog'] ?></td>
</tr>
<?php
      $class = alt_row_class($class);
    }
    echo '</table>';
  }
  else
    echo '<p>This resident has no trade history.</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
