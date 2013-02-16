<?php
$whereat = 'bank';
$wiki = 'Trading_House';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/tradelib.php';

if($NO_PVP)
{
  header('Location: /lostdata.php');
  exit();
}

if($user['license'] == 'no')
{
  header('Location: /ltc.php?dialog=2');
  exit();
}

consider_new_trade_flag($user['idnum']);

$url_params = array();

if($_GET['view'] == 'complete')
{
  $command = 'SELECT * FROM `monster_trades` WHERE (userid1=' . $user['idnum'] . ' OR userid2=' . $user['idnum'] . ') ' .
             'AND `step`=3 ORDER BY `step` ASC,tradeid DESC';

  $url_params[] = 'view=complete';
}
else if($_GET['view'] == 'cancelled')
{
  $command = 'SELECT * FROM `monster_trades` WHERE (userid1=' . $user['idnum'] . ' OR userid2=' . $user['idnum'] . ') ' .
             'AND `step`=4 ORDER BY `step` ASC,tradeid DESC';

  $url_params[] = 'view=cancelled';
}
else
{
  $_GET['view'] = 'pending';

  $command = 'SELECT * FROM `monster_trades` WHERE (userid1=' . $user['idnum'] . ' OR userid2=' . $user['idnum'] . ') ' .
             'AND `step` IN (1, 2) ORDER BY `step` ASC,tradeid DESC';
}

$count_command = str_replace('*', 'COUNT(*)', $command);
$data = $database->FetchSingle($count_command);
$trade_count = (int)$data['COUNT(*)'];

$num_pages = ceil($trade_count / 20);

$page = (int)$_GET['page'];
if($page < 1 || $page > $num_pages)
  $page = 1;

$command .= ' LIMIT ' . (($page - 1) * 20) . ',20';

if($trade_count > 0)
  $trades = $database->FetchMultiple($command, 'fetching resident\'s private trades');

function list_trade_items($itemlist)
{
  $items = explode('<br />', $itemlist);
  foreach($items as $data)
  {
    $item = explode(';', $data);
    $details = get_item_byname($item[0]);
    $num = (int)$item[1];

    if($num > 1)
      echo $num . '&times; ';

    echo '<a href="encyclopedia2.php?item=' . link_safe($item[0]) . '">' . $item[0] . '</a>';

    if($details['custom'] == 'yes')
      echo ' (custom)';

    echo '<br />';
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Trading House</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Trading House</h4>
     <ul class="tabbed">
      <li><a href="/trading_public2.php">Public Trade Offers</a></li>
      <li class="activetab"><a href="/trading.php">Private Trading</a></li>
     </ul>
     <p><i>(In order for two residents to trade, </i>both<i> must have a License to Commerce.)</i></p>
     <ul>
      <li><a href="/newtrade.php">Initiate a new trade</a></li>
     </ul>
     <ul class="tabbed">
      <li<?= ($_GET['view'] == 'pending') ? ' class="activetab"' : '' ?>><a href="/trading.php">Pending Trades</a></li>
      <li<?= ($_GET['view'] == 'complete') ? ' class="activetab"' : '' ?>><a href="/trading.php?view=complete">Completed Trades</a></li>
      <li<?= ($_GET['view'] == 'cancelled') ? ' class="activetab"' : '' ?>><a href="/trading.php?view=cancelled">Cancelled Trades</a></li>
     </ul>
<?php
if($trade_count > 0)
{
  $url_params[] = 'page=%s';
  $page_list = paginate($num_pages, $page, 'trading.php?' . implode('&amp;', $url_params));
?>
     <?= $page_list ?>
     <table>
      <tr class="titlerow">
       <th>Action</th>
       <th>Resident</th>
       <th>Sending</th>
       <th>Receiving</th>
       <th>Message</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($trades as $trade)
  {
    if($trade['userid1'] == $user['idnum'])
    {
      $position = 1;
      $antiposition = 2;
      $other_user = $trade['userid2'];
    }
    else
    {
      $position = 2;
      $antiposition = 1;
      $other_user = $trade['userid1'];
    }

    $target = get_user_byid($other_user);
?>
      <tr class="<?= $rowclass ?>">
       <td valign="top">
<?php
//    if($admin["clairvoyant"] == "yes") echo "(" . $trade["step"] . ") ";

    switch($trade['step'])
    {
      case 1:
        if($position == 1)
          echo 'Waiting&nbsp;for&nbsp;response';
        else
        {
          if($trade['gift'] == 'yes')
            echo '<a href="/accepttrade.php?tradeid=' . $trade['tradeid'] . '">Accept&nbsp;gift</a>';
          else
            echo '<a href="/negotiatetrade.php?tradeid=' . $trade['tradeid'] . '">Negotiate&nbsp;trade</a>';
        }
        break;
      
      case 2:
        if($position == 1)
          echo '<a href="accepttrade.php?tradeid=' . $trade['tradeid'] . '">Accept&nbsp;trade</a>';
        else
          echo 'Waiting&nbsp;for&nbsp;response';
        break;
      
      case 3:
        echo 'Trade&nbsp;successful';
        break;
      
      case 4:
        echo 'Trade&nbsp;cancelled';
        break;
      
      default:
        echo '<i>undefined&nbsp;status</i>';
    }

    echo '<br />';

    if($trade['step'] < 3)
      echo '<a href="/canceltrade.php?tradeid=' . $trade['tradeid'] .'">Cancel trade</a><br />';

    if($trade['anonymous'] == 'yes' && $trade['userid1'] != $user['idnum'])
      $other_resident = '<i>anonymous</i>';
    else
      $other_resident = '<nobr>' . resident_link($target['display']) . '</nobr>';
?>
       </td>
       <td valign="top"><?= $other_resident ?></td>
       <td valign="top">
<?php
    if($trade['money' . $position] > 0)
      echo $trade['money' . $position] . '<span class="money">m</span><br />';

    if(strlen($trade['itemsdesc' . $position]) > 0)
      list_trade_items($trade['itemsdesc' . $position]);
?>
       </td>
       <td valign="top">
<?php
    if($trade['money' . $antiposition] > 0)
      echo $trade['money' . $antiposition] . '<span class="money">m</span><br />';

    if(strlen($trade['itemsdesc' . $antiposition]) > 0)
      list_trade_items($trade['itemsdesc' . $antiposition]);
?>
       </td>
       <td valign="top"><?= $trade['dialog'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <?= $page_list ?>
<?php
}
else
  echo '<p>There is no trading information to display.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
