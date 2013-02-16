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

if($user['bankflag'] == 'yes')
{
  clear_madesale($user['idnum']);
  $user['bankflag'] = 'no';
}

if($_POST['submit'] == 'Transact')
{
  if(preg_match("/^([0-9]+)$/", $_POST['amount']))
  {
    // user deposits
    if($_POST['action'] == 'deposit')
    {
      $amount = (int)$_POST['amount'];
      if($user['money'] >= $amount)
      {
        $command = 'UPDATE monster_users ' .
                   'SET money=money-' . $amount . ', ' .
                       'savings=savings+' . $amount . ' ' .
                   'WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'bank.php');

        $user['money'] -= $amount;
        $user['savings'] += $amount;
          
        $error_message = 'Done!  Is there anything else I can help you with?';
 
        $_POST['amount'] = '';
        $_POST['action'] = '';
      }
      else
        $error_message = 'You do not have ' . $amount . '<span class="money">m</span> on hand...';
    }
    // withdraw
    else if($_POST['action'] == 'withdraw')
    {
      $amount = (int)$_POST['amount'];
      if($user['savings'] >= $amount)
      {
        $command = 'UPDATE monster_users ' .
                   'SET money=money+' . $amount . ', ' .
                       'savings=savings-' . $amount . ' ' .
                   'WHERE `user`=' . quote_smart($user['user']) . ' LIMIT 1';
        $database->FetchNone($command, 'bank.php');

        $user['money'] += $amount;
        $user['savings'] -= $amount;

        $error_message = 'Done!  Is there anything else I can help you with?';
 
        $_POST['amount'] = '';
        $_POST['action'] = '';
      }
      else
        $error_message = 'You don\'t have ' . $amount . '<span class="money">m</span> in savings, and no: I won\'t give you a loan.';
    }
    else
      $error_message = 'Do what now?';
  }
  else
    $error_message = 'Sorry, how many moneys did you say?  Maybe I misheard, but that didn\'t sound like a number...';
}
else if($_POST['action'] == 'cleartransactions')
{
  clear_transactions($user['user']);
}
else if($_POST['action'] == 'updatebilling')
{
  if($_POST['allowbilling'] == 'yes' || $_POST['allowbilling'] == 'on')
    $user['savings_pay_storage'] = 'yes';
  else
    $user['savings_pay_storage'] = 'no';

  $command = 'UPDATE monster_users SET savings_pay_storage=' . quote_smart($user['savings_pay_storage']) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
  $database->FetchNone($command, 'bank.php');
  
  $error_message = 'Your billing options have been updated.';
}

$badges = get_badges_byuserid($user['idnum']);

if($badges['thousandaire'] == 'no' && $user['money'] + $user['savings'] >= 1000)
{
  $got_badge_thousandaire = true;
  set_badge($user['idnum'], 'thousandaire');
}
else if($badges['millionaire'] == 'no' && $user['money'] + $user['savings'] >= 1000000)
{
  $got_badge_millionaire = true;
  set_badge($user['idnum'], 'millionaire');
}

$transactions = array();

$command = 'SELECT COUNT(*) AS c FROM monster_transactions WHERE `user`=' . quote_smart($user['user']);
$data = $database->FetchSingle($command, 'fetching transaction count');

$num_pages = ceil($data['c'] / 20);

$page = (int)$_GET['page'];
if($page > $num_pages)
  $page = $num_pages;
if($page < 1)
  $page = 1;

$command = 'SELECT * FROM monster_transactions  WHERE `user`=' . quote_smart($user['user']) . ' ORDER BY timestamp DESC LIMIT ' . (($page - 1) * 20) . ',20';
$transactions = $database->FetchMultiple($command, 'bank.php');

$hephaestus_charm_quest = get_quest_value($user['idnum'], 'hephaestus charm');

if($hephaestus_charm_quest['value'] == 1)
{
  if($_GET['dialog'] == 'hephaestuscharm')
  {
    $dialog = '<p>I definitely gave Matalie that charm, but I certainly didn\'t make it myself!  Don\'t be ridiculous.  I bought it from... well, you don\'t think I\'d just <em>tell</em> you, do you?</p>';
  
    $options[] = '<a href="?dialog=priceofinformation">Ask how much the information will cost</a>';
  }
  else if($_GET['dialog'] == 'priceofinformation')
  {
    $dialog = '<p>Ho-ho-ho!  I\'m kidding, ' . $user['display'] . '!  You\'re so gullible.  Besides, I thought you\'d already know that Thaddeus sells them.  It\'s not like it\'s a big secret, or anything.</p><p class="size9">Though I wonder how much you were prepared to pay... <i>(she looks you over, as if to gauge your wealth).</i></p>';
    
    update_quest_value($hephaestus_charm_quest['idnum'], 2);
  }
  else
    $options[] = '<a href="?dialog=hephaestuscharm">Ask about the Hephaestus Charm</a>';
}

$haerts_quest = get_quest_value($user['idnum'], 'haerts quest');

if($haerts_quest === false)
{
  if($user['idnum'] <= 35942)
  {
    if($_GET['dialog'] == 'typocompensation')
    {
      add_quest_value($user['idnum'], 'haerts quest', 1);
      add_inventory($user['user'], '', 'Haerts', 'Given to you by Lakisha', $user['incomingto']);
      $error_message = 'Oh yes, I was told to expect this.  It\'s because ' . $SETTINGS['author_resident_name'] . ' left a bunch of typos unchecked for so long, or something, right?</p><p>Well, anyway, here you go.  Enjoy it.</p><p><i>(You received Haerts.  Find it in ' . $user['incomingto'] . '.)</i></p>';
    }
    else
      $options[] = '<a href="bank.php?dialog=typocompensation">Ask for the "typo compensation" item</a>';
  }
}

$shrine_quest = get_quest_value($user['idnum'], 'shrine quest');

if($shrine_quest['value'] == 2)
{
  if($_GET['dialog'] == 3)
  {
    $shrine_dialog = true;
    update_quest_value($shrine_quest['idnum'], 3);
    $shrine_quest['value'] = 3;
  }
}
else if($shrine_quest['value'] == 3)
{
  if($_GET['dialog'] == 4)
  {
    $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=\'Limeade\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
    $data = $database->FetchSingle($command, 'fetching limeade for banker');

    $limeade_count = $data['COUNT(*)'];

    if($limeade_count >= 10)
    {
      delete_inventory_byname($user['user'], 'Limeade', 10, 'storage');
      $limeade_thankyou = true;
      update_quest_value($shrine_quest['idnum'], 4);
      $shrine_quest['value'] = 4;
    }
    else
      $limeade_dialog = true;
  }
}

$st_patricks_bank = get_quest_value($user['idnum'], 'stpat bank ' . date('Y') . ' reward');
$st_patricks_totem = get_quest_value($user['idnum'], 'stpat totem ' . date('Y') . ' reward');

if($st_patricks_bank['value'] > 0 || $st_patricks_totem['value'] > 0)
{
  if($_GET['dialog'] == 'stpatrick')
  {
    require_once 'commons/statlib.php';

    $dialog = '<p>Right, then.  Here you go:</p>';

    if($st_patricks_bank['value'] > 0)
    {
      $rewards[] = 'For helping me out, take this: Lakisha\'s Bracelet';
      add_inventory($user['user'], 'u:28357', 'Lakisha\'s Bracelet', 'For helping Lakisha on St. Patrick\'s Day', 'storage/incoming');

      if($st_patricks_bank['value'] > 1)
      {
/*        $rewards[] = 'And for being one of the top 10: 50 Favor Ticket';
        add_inventory($user['user'], 'u:28357', '50 Favor Ticket', 'For helping Lakisha on St. Patrick\'s Day', 'storage/incoming');*/
      }

      flag_new_incoming_items($user['user']);
      update_quest_value($st_patricks_bank['idnum'], 0);
    }

    if($st_patricks_totem['value'] > 0)
    {
      $rewards[] = 'For helping Matalie out, take this: Matalie\'s Hair Pin';
      add_inventory($user['user'], 'u:28356', 'Matalie\'s Hair Pin', 'For helping Matalie on St. Patrick\'s Day', 'storage/incoming');

      if($st_patricks_totem['value'] > 1)
      {
/*        $rewards[] = 'And for being one of the top 10: 50 Favor Ticket';
        add_inventory($user['user'], 'u:28356', '50 Favor Ticket', 'For helping Matalie on St. Patrick\'s Day', 'storage/incoming');*/
      }

      flag_new_incoming_items($user['user']);
      update_quest_value($st_patricks_totem['idnum'], 0);
    }

    record_stat($user['idnum'], 'St. Patrick\'s Day Rewards', count($rewards));

    $dialog .= '<ul><li>' . implode('</li><li>', $rewards) . '</li></ul>';

    if($st_patricks_bank['value'] == 1 && $st_patricks_totem['value'] == 1)
      $dialog .= '<p>Whose side are you on, anyway?</p>';
    else if($st_patricks_bank['value'] > 1 && $st_patricks_totem['value'] > 1)
    {
      $dialog .= '<p>I can\'t help but feel like you took advantage of our little competition, ' . $user['display'] . '... not that I don\'t admire your duplicity - it seems like something I would have done, in your place...</p><p>That being said, I <em>don\'t</em> like being taken advantage of.  I\'ll be keeping my eye on you.</p>';
      add_quest_value($user['idnum'], 'stpat duplicity ' . date('Y'), 1);
    }
  }
  else
    $options[] = '<a href="bank.php?dialog=stpatrick">Ask about your St. Patrick\'s Day Competition reward</a>';
}

$duplicity = get_quest_value($user['idnum'], 'stpat duplicity ' . date('Y'));

if($badges['horseshoe'] == 'no' && $duplicity === false)
{
  $command = 'SELECT COUNT(*) FROM monster_inventory WHERE itemname=\'Horseshoes\' AND user=' . quote_smart($user['user']) . ' AND location=\'storage\'';
  $data = $database->FetchSingle($command, 'fetching horseshoes for banker');

  $horseshoe_count = (int)$data['COUNT(*)'];
  if($horseshoe_count > 0)
  {
    if($_GET['dialog'] == 'horseshoes')
    {
      set_badge($user['idnum'], 'horseshoe');
      delete_inventory_byname($user['user'], 'Horseshoes', 1, 'storage');
      $horseshoe_dialog_thanks = true;
    }
    else
      $horseshoe_dialog = true;
  }
}

$mahjong_time = get_quest_value($user['idnum'], 'last mahjong supply');

if($mahjong_time !== false)
{
  if($_GET['dialog'] == 'mahjong')
  {
    if($duplicity !== false)
      $dialog = '<p>Hmph!  Are you here trying to play both sides, again?</p>';
    else
      $dialog = '<p>Ahahaha!  Silly Matalie.  Tony\'s been supplying me with tiles for years.</p>';
  }
  else
    $options[] = '<a href="bank.php?dialog=mahjong">Tell her about Matalie\'s Mahjong Exchange.</a>';
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Bank</title>
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
     <h4>The Bank</h4>
     <ul class="tabbed">
      <li class="activetab"><a href="/bank.php">The Bank</a></li>
      <li><a href="/bank_groupcurrencies.php">Group Currencies</a></li>
      <li><a href="/bank_exchange.php">Exchanges</a></li>
      <li><a href="/ltc.php">License to Commerce</a></li>
      <li><a href="/allowance.php">Allowance Preference</a></li>
<?= $st_patricks ? '<li class="stpatrick"><nobr><a href="/stpatricks.php?where=bank">St. Patrick\'s Day Competition</a></nobr></li>' : '' ?>
     </ul>
<?php
// BANKER LAKISHA
echo '<a href="/npcprofile.php?npc=The Banker"><img src="//' . $SETTINGS['static_domain'] . '/gfx/npcs/thebanker.png" align="right" width="350" height="" alt="(The Banker)" /></a>';

include 'commons/dialog_open.php';

$ask_questions = true;

if($error_message)
  echo "<p>$error_message</p>";
else if($dialog != '')
  echo $dialog;
else
{
  if($got_badge_thousandaire)
  {
    echo '<p>Congratulations on your fortune, ' . $user['display'] . '.  1,000<span class="money">m</span>!  You\'re moving up in the world.</p>' .
         '<p><i>(You won the Thousandaire Badge!)</i></p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($got_badge_millionaire)
  {
    echo '<p>Congratulations on your fortune, ' . $user['display'] . '.  1,000,000<span class="money">m</span>!  Impressive.  Of course I\'ve had mine for a while now.</p>' .
         '<p><i>(You won the Millionaire Badge!)</i></p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($got_badge_billionaire)
  {
    echo '<p>Congratulations on your fortune, ' . $user['display'] . '.  1,000,000,000<span class="money">m</span>!!  Simply amazing.  Even <em>I</em> wouldn\'t know what to do with so many moneys.</p>' .
         '<p><i>(You won the Billionaire Badge!)</i></p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($shrine_dialog)
  {
    echo '<p>Oh, no, she doesn\'t need to worry about a thing.  A lot more people are going to be showing up than expected, so everything\'s being delayed as I arrange for a bigger space.</p>' .
         '<p>Actually, maybe you can help me out:  I\'ve got most of the food taken care of, but there\'s some trouble with the refreshments, and I\'m 10 Limeade short...</p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($limeade_dialog)
  {
    if($limeade_count > 0)
      echo '<p>It looks like you only have ' . $limeade_count . ' Limeade in your storage...</p>';
    else
      echo '<p>You don\'t have <em>any</em> Limeade in your storage yet...</p>';
  
    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($limeade_thankyou)
  {
    echo '<p>Thanks, ' . $user['display'] . '.  You\'ve been a big help.</p>' .
         '<p>Can I ask you to do one last, small thing?  I sent Nina an RSVP, but haven\'t heard back from her yet.  Can you go find out whether or not she\'s coming?</p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($_GET['dialog'] == 1)
  {
    echo '<p>The Bank\'s interest rate is ' . (interest_rate() * 100) . '%, compounded daily.  There <em>is</em> a maximum earning of ' . value_with_inflation(50) . ' moneys per day, however.</p>' .
         '<p>Items sell back to "the game" at ' . (sellback_rate() * 100) . '% their market value (rounded up).</p>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
  }
  else if($_GET['dialog'] == 2)
  {
    require_once 'commons/questlib.php';

    $sellback = get_quest_value($user['idnum'], 'total sellback');
    $sellback_value = (int)$sellback['value'];

    if($sellback_value > 0)
      $sellback_extra = ' So far, you\'ve sold back a total of ' . $sellback_value . ' moneys worth of items.';
    else
      $sellback_extra = ' You haven\'t sold anything back yet.';

    echo '<p>There are a few good ways to make money.  You can\'t rely on <a href="allowance.php">your allowance</a> forever!</p>' .
         '<ul class="spacedlist"><li>Sell items that your pets bring home (you can sell items for a small amount from your <a href="storage.php">Storage</a>), especially non-food items.  The Pillow and Picture you start with should be enough for a single, low-level pet.' . $sellback_extra . '</li>' .
         '<li>Put excess money in the bank.  It helps prevent you from spending it, and you earn a <a href="bank.php?dialog=1">small amount of interest</a>.</li>' .
         '<li>Don\'t buy things you don\'t need!  Save up for a <a href="ltc.php">License to Commerce</a>!  (It costs ' . value_with_inflation(500) . '<span class="money">m</span>.)</li>';
    if($user['show_park'] == 'yes')
      echo '<li>Once you have a License to Commerce, <a href="/hostevent1.php">host park events</a>.  These are a great way to make a few moneys.</li>';
    echo '<li>The License to Commerce also lets you sell items to other players (via the <a href="auctionhouse.php">Auction House</a>, or your own <a href="mystore.php">personal store</a>).  You can usually sell items to other players for a higher price than you can sell them using the "Gamesell" button in your Storage.</li></ul>';

    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($_GET['dialog'] == 'fsd')
  {
    echo '<p>You\'re wondering about Free Storage Days?</p>' .
         '<p>There are a few days of the year during which time we do not charge storage fees, <em>if you are active on that day</em>.</p>' .
         '<p>Additionally, any days you may have been gone <em>just before</em> a Free Storage Day will also have storage fees waived.</p>' .
         '<p><i>(If you log on during a Free Storage Day, the Storage fees for that day, as well as every day since you were last active, will not be charged.)</i></p>' .
         '<p>The Free Storage Days are:</p>' .
         '<ul><li>' .
         implode('</li><li>', $FREE_STORAGE_DAYS) .
         '</li></ul>';
    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($_GET['dialog'] == 'spending')
  {
    echo '
      <p>Oh, goodness!  Lots of things!</p>
      <p>If you need food, you can get some at the <a href="grocerystore.php">Grocery Store</a>.  You might also check out <a href="recycling_gamesell.php">Ian\'s "Refuse Store"</a>; there\'s a lot of odd stuff for sale there.</p>
    ';

    if($user['show_park'] == 'yes')
      echo '<p><a href="park.php">Park events</a> will help pets develop their skills, especially young pets, and some offer prizes.  They also typically have entrance fees you\'ll need to pay.</p>';
    
    echo '<p>With the License to Commerce, you could check out other Residents\' stores at the <a href="itemsearch.php">Flea Market</a>, or place bids at the <a href="auctionhouse.php">Auction House</a> or <a href="reversemarket.php">Seller\'s Market</a>.</p>';

    $options[] = '<a href="ltc.php">Ask about the License to Commerce</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }
  else if($horseshoe_dialog)
  {
    echo '<p>Oh, ' . $user['display'] . ', are you also interested in horseback riding?</p>' .
         '<p>On my days off, Clyde - my horse - and I often go out riding.</p>' .
         '<p>Mm... I hate to impose, but could I possibly borrow your Horseshoes?  During our last ride we took a break by a stream, and a Rogue Broccoli made off with our only pair.  Clyde loves to dangle his bare hooves in the stream, you see...</p>';
    $options[] = '<a href="bank.php?dialog=horseshoes">Give her your Horseshoes from Storage</a>';
  }
  else if($horseshoe_dialog_thanks)
  {
    echo '<p>Thanks a lot!  I was kind of worried we wouldn\'t be able to go riding at all this weekend!</p>' .
         '<p>Hey, until I give these back, why don\'t you hang on to this Horseshoe Badge?  You know, as collateral.</p>' .
         '<p><i>(You received the Horseshoe Badge!)</i></p>';
  }
  else
  {
    echo '<p>Welcome to The Bank.  What can I help you with today?</p>';
    $options[] = '<a href="bank.php?dialog=spending">Ask what can be purchased with money</a>';
    $options[] = '<a href="bank.php?dialog=2">Ask about making money</a>';
    $options[] = '<a href="bank.php?dialog=1">Ask about various rates</a>';
  }

  if($shrine_quest['value'] == 2)
    $options[] = '<a href="bank.php?dialog=3">Explain about Vanessa\'s delay</a>';
  else if($shrine_quest['value'] == 3 && !$limeade_dialog)
    $options[] = '<a href="bank.php?dialog=4">Offer 10 Limeade</a>';
}

include 'commons/dialog_close.php';

if(count($options) > 0)
  echo '<ul><li>' . implode('</li><li>', $options) . '</li></ul>';

if(strlen($_GET["msg"]) > 0)
  $error_message = form_message(explode(",", $_GET["msg"]));
?>
     <p>Bank Balance: <?= sprintf('%01.2f', $user['savings']) ?><span class="money">m</span></p>
     <form method="post">
     <table>
      <tr>
       <td>
        <input type="radio" name="action" value="deposit" <?= ($_POST['action'] != 'withdraw' ? 'checked' : '') ?> id="transaction_deposit"><label for="transaction_deposit"> Deposit</label><br />
        <input type="radio" name="action" value="withdraw" <?= ($_POST['action'] == 'withdraw' ? 'checked' : '') ?>  id="transaction_withdraw"><label for="transaction_withdraw"> Withdraw</label><br />
       </td>
       <td><input name="amount" style="width:64px;" value="<?= $_POST['amount'] ?>" /></td>
       <td><input type="hidden" name="submit" value="Transact" /><input type="submit" value="Transact" /></td>
      </tr>
     </table>
     </form>
     <h5>Billing Options</h5>
     <form method="post">
     <p><input type="checkbox" name="allowbilling"<?= $user['savings_pay_storage'] == 'yes' ? ' checked' : '' ?> /> Allow money from Savings to be automatically withdrawn to pay daily fees if you don't have enough on-hand.  (A 1<span class="money">m</span> service fee will be charged every time this is done.)</p>
     <p><input type="hidden" name="action" value="updatebilling" /><input type="submit" value="Confirm" /></p>
     </form>
     <h5 id="transactions">Transaction History</h5>
<?php
if(count($transactions) > 0)
{
  $pagination = paginate($num_pages, $page, '?page=%d#transactions');
?>
     <p><i>(Hover over the magnifying glass icons for detailed reports.<?php if($user['license'] == 'yes') { ?>  For a history of trades, see <a href="trading.php">Private Trading</a>.<?php } ?>)</i></p>
     <?= $pagination ?>
     <table>
      <tr class="titlerow">
       <th>Time&nbsp;Stamp</th>
       <th>Amount</th>
       <th></th>
       <th>Description</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($transactions as $transact)
  {
    if(strlen($transact['details']) > 0)
    {
      $details_hover = ' onmouseover="Tip(\'' . tip_safe($transact['details']) .'\')"';
      $icon = '<img src="/gfx/search.gif" />';
    }
    else
    {
      $details_hover = '';
      $icon = '';
    }
?>
      <tr class="<?= $rowclass ?>"<?= $details_hover ?>>
       <td><nobr><?= local_time($transact['timestamp'], $user['timezone'], $user['daylightsavings']) ?></nobr></td>
       <td align="right"><?= $transact['amount'] ?><span class="money">m</span></td>
       <td><?= $icon ?></td>
       <td><?= format_text($transact['description']) ?></td>
      </tr>
<?php

    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
     <?= $pagination ?>
     <form action="bank.php" method="post"><p><input type="hidden" name="action" value="cleartransactions" /><input type="submit" value="Clear History" class="bigbutton" /></p></form>
<?php
}
else
  echo '     <p>No transactions have been recorded.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
