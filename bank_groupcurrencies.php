<?php
$whereat = 'bank';
$wiki = 'The_Bank';
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/inventory.php';
require_once 'commons/formatting.php';
require_once 'commons/messages.php';
require_once 'commons/userlib.php';
require_once 'commons/questlib.php';
require_once 'commons/economylib.php';

$command = '
  SELECT
    a.currencyid,
    a.amount,
    a.hidden,
    b.name,
    b.symbol,
    b.groupid,
    c.name AS group_name,
    c.leaderid
  FROM
    psypets_group_player_currencies AS a
    LEFT JOIN psypets_group_currencies AS b
      ON a.currencyid=b.idnum
    LEFT JOIN psypets_groups AS c
      ON b.groupid=c.idnum
  WHERE
    a.userid=' . $user['idnum'] . '
';
$group_currencies = $database->FetchMultiple($command, 'fetching extra currencies');

if($_POST['action'] == 'Apply')
{
  foreach($group_currencies as $i=>$currency)
  {
    $id = $currency['currencyid'];
    
    if($_POST['currency_' . $id] == 'yes' || $_POST['currency_' . $id] == 'on')
    {
      if($currency['hidden'] == 'yes')
      {
        $command = 'UPDATE psypets_group_player_currencies SET hidden=\'no\' WHERE userid=' . $user['idnum'] . ' AND currencyid=' . $id . ' LIMIT 1';
        $database->FetchNone($command, 'updating settings');
        $group_currencies[$i]['hidden'] = 'no';
      }
    }
    else
    {
      if($currency['hidden'] == 'no')
      {
        $command = 'UPDATE psypets_group_player_currencies SET hidden=\'yes\' WHERE userid=' . $user['idnum'] . ' AND currencyid=' . $id . ' LIMIT 1';
        $database->FetchNone($command, 'updating settings');
        $group_currencies[$i]['hidden'] = 'yes';
      }
    }
  }
}


include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Bank &gt; Group Currencies</title>
<?php include 'commons/head.php'; ?>
  <script type="text/javascript">
   function check_all()
   {
     i = document.trades.elements.length;
     for(j = 1; j < i; ++j)
     {
       document.trades.elements[j].checked = document.trades.checkall.checked;
     }
   }
  </script>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Bank &gt; Group Currencies</h4>
     <ul class="tabbed">
      <li><a href="bank.php">The Bank</a></li>
      <li class="activetab"><a href="bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="bank_exchange.php">Exchanges</a></li>
      <li><a href="ltc.php">License to Commerce</a></li>
      <li><a href="allowance.php">Allowance Preference</a></li>
<?= $st_patricks ? '<li class="stpatrick"><a href="stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="npcprofile.php?npc=Lakisha+Pawlak"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/banker_lakisha.png" align="right" width="350" height="427" alt="(Lakisha the Banker)" /></a>';

include 'commons/dialog_open.php';

if($_GET['dialog'] == 'splain')
{
  echo '
    <p>The distribution and use of a Group Currency is handled entirely by the group who created that currency.  The group organizer may, without restriction, give or take their currencies from any resident.</p>
    <p>That may sound scary, but don\'t worry: most group organizers are decent enough!</p>
    <p>Remember that a Group Currency does not necessarily represent material wealth.  A group may use a currency to measure status, rank, good deeds, or anything else; how it\'s used is entirely up to the group organizer.</p>
    <p>If you have questions about what you can do with some Group Currency you\'ve received, check out the group\'s page or forums - that\'s usually where the currency would be explained - or ask the group\'s organizer directly.</p>
  ';
}
else
{
  echo '
    <p>If you\'ve received any Group Currencies, you can review them here.</p>
    <p>If you don\'t like seeing a particular Group Currency on the top of every page, let me know, and I\'ll hide it for you.  You can always come back here to review the currencies you\'ve received, and change your preferences on which are shown.</p>
  ';
  
  $options[] = '<a href="bank_groupcurrencies.php?dialog=splain">Ask what Group Currencies are all about</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if(strlen($_GET['msg']) > 0)
  $error_message = form_message(explode(',', $_GET['msg']));

echo '
  <form method="post">
  <table>
   <thead>
    <tr class="titlerow">
     <th class="centered"><img src="gfx/the_eye.png" width="15" height="15" alt="Visible?" title="Visible?" style="display:block;" /></th><th colspan="2" class="centered">Currency</th><th>Group</th><th>Group Organizer</th>
    </tr>
   </thead>
   <tbody>
';

if(count($group_currencies) > 0)
{
  $rowclass = begin_row_class();

  foreach($group_currencies as $currency)
  {
    $leader = get_user_byid($currency['leaderid'], 'display');
  
    echo '
      <tr class="' . $rowclass . '">
       <td class="centered"><input type="checkbox" name="currency_' . $currency['currencyid'] . '"' . ($currency['hidden'] == 'no' ? ' checked="checked"' : '') . ' /></td>
       <td class="righted">' . $currency['amount'] . '</td><td><abbr title="' . $currency['name'] . '">' . $currency['symbol'] . '</abbr></td>
       <td><a href="grouppage.php?id=' . $currency['groupid'] . '">' . $currency['group_name'] . '</a></td>
       <td>' . resident_link($leader['display']) . '</td>
      </tr>
    ';
    
    $rowclass = alt_row_class($rowclass);
  }
}

echo '
   </tbody>
  </table>
  <p><input type="submit" name="action" value="Apply" /></p>
  </form>
';

?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
