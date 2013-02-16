<?php
require_once 'commons/formatting.php';

if($_user && $_user->IsLoaded()) $user = $_user->RawData();
if($_house && $_house->IsLoaded()) $house = $_house->RawData();

if($user['idnum'] > 0)
{
  if(($user['show_tip'] == 'yes' && $no_tip !== true) || array_key_exists('tipoftheday', $_GET))
  {
    require_once 'commons/tipoftheday.php';

    $tip = tip_index($user['tip_number']);

    $command = 'UPDATE monster_users SET show_tip=\'no\',tip_number=' . ($tip + 1) . ' WHERE idnum=' . $user['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'topbar.php');

    if($tip >= count($TIP_TEXT) - 1)
    {
      require_once 'commons/questlib.php';

      $quest_val = get_quest_value($user['idnum'], 'Tip Quest!');
      if($quest_val === false)
      {
        add_quest_value($user['idnum'], 'Tip Quest!', 1);
        $tip = count($TIP_TEXT) - 1;
      }
    }
?>
<div id="tip" style="display:none;">
<h5>Fancy-useful Tip <?= $tip + 1 ?> of <?= count($TIP_TEXT) ?></h5>
<center><?= $TIP_TEXT[$tip] ?></center>
<a href="#" id="close_tip">close</a>
</div>
<script type="text/javascript">
$(function() {
  $('#tip').css({left: (($(document).width() - $('#tip').width()) / 2) + 'px'});
  $('#tip').show();

  $('#close_tip').click(function() {
    $('#tip').fadeOut();
    return false;
  });
});
</script>
<?php
  }

  echo '<div id="loggedin"><div id="logout">';

  if($user['admin']['admintools'] == 'yes')
    echo '<a href="/admin/tools.php"><img src="//' . $SETTINGS['static_domain'] . '/gfx/ui/wrench.png" height="16" width="16" class="inlineimage" alt="Admin tools" /></a>';

  echo '<a href="/myaccount/statsc.php"><img src="/gfx/stats.png" alt="stats" title="Account Statistics" width="16" height="16" class="inlineimage" /></a> | <a href="/myaccount/">My Account</a> | <a href="/logout.php">Log Out</a></div>',
       '<div id="resident-status"><a href="/residentprofile.php?resident=' . link_safe($user['display']) . '">' . $user['display'] . ', ' . $user['title'] . '</a>';

  if($user['idnum'] > 0)
  {
    $q_user = quote_smart($user['user']);

    // --- administrative abuse reports ------------
    if($admin['abusewatcher'] == 'yes')
    {
      $command = 'SELECT COUNT(idnum) AS c FROM psypets_abusereports';
      $data = fetch_single($command, 'fetching abuse report count');
      if($data['c'] > 0)
        $alert .= '<a href="/admin/abusereports.php"><img src="/gfx/abusereport.png" height="16" width="16" title="Abuse reports require your attention!" alt="abuse report" class="inlineimage" /></a>';
    }

    // --- new city hall post? ---------------------
    if($user['newcityhallpost'] == 'yes')
      $alert .= '<a href="/cityhall.php"><img src="/gfx/newpost.gif" height="16" width="16" title="A new public announcement has been made." alt="new announcement" class="inlineimage" /></a>';

    if($user['newpoll'] == 'yes')
      $alert .= '<a href="/pollstandalone.php"><img src="/gfx/newpoll.png" height="16" width="16" title="A new poll has been posted." alt="new poll" class="inlineimage" /></a>';

    if($user['newchangelogentries'] == 'yes')
      $alert .= '<a href="/changelog.php"><img src="/gfx/newchangelog.png" height="16" width="16" title="A new changelog entry has been made." alt="new changelog entry" class="inlineimage" /></a>';

    if($user['pvp_message'] == 'yes')
      $alert .= '<a href="/myhouse/addon/airship_mooring.php"><img src="/gfx/pvp.png" height="16" width="16" title="You\'ve been attacked!" alt="PvP attack" class="inlineimage" /></a>';

    // --- to-do list updates ----------------------
    if($user['wishlistupdate'] == 'yes')
      $alert .= '<a href="/arrangewishes.php"><img src="/gfx/todolist_add.png" height="16" width="16" title="A To-do List item has been added!" alt="to-do list addition" class="inlineimage" /></a>';

    // --- check for group requests ----------------
    if($user['newgroupinvite'] == 'yes')
      $alert .= '<a href="/mygroups.php"><img src="/gfx/grouprequest.png" width="16" height="16" title="You\'ve been invited to join a group!" alt="group invite" class="inlineimage" /></a>';

    // --- check for friending alerts ----------------
    if($user['newfriend'] == 'yes')
      $alert .= '<a href="/myfriends.php"><img src="/gfx/newfriend.png" width="16" height="16" title="Someone added you as a friend!" alt="new friend" class="inlineimage" /></a>';

    // --- check for new mail ----------------------
    if($user['newmail'] == 'yes')
      $alert .= '<a href="/post.php"><img src="/gfx/newmail.png" height="16" width="16" title="You have new mail!" alt="new mail" class="inlineimage" /></a>';

    // --- check for new packages ------------------
		$incoming = $database->FetchMultiple(
			'SELECT idnum FROM monster_inventory ' .
			"WHERE `user`=$q_user AND location='storage/incoming' LIMIT 2"
		);

    if(count($incoming) > 0)
      $alert .= '<a href="/incoming.php"><img src="/gfx/newpackage.png" height="16" width="16" title="You have ' . (count($incoming) > 1 ? 'items' : 'an item') . ' waiting in Incoming." alt="incoming items" class="inlineimage" /></a>';

    // --- check for new trades --------------------
    if($user['newtrade'] == 'yes')
      $alert .= '<a href="/trading.php"><img src="/gfx/newtrade.png" height="16" width="16" title="One or more trades are waiting for your response." alt="new trade" class="inlineimage" /></a>';

    if($user['new_bid'] == 'yes')
      $alert .= '<a href="/trading_public2.php?myoffers=1"><img src="/gfx/newbid.png" height="16" width="16" title="One or more bids have been made on your public trade offers." alt="new public trade bid" class="inlineimage" /></a>';

    // --- check for notable bank transactions -----
    if($user['bankflag'] == 'yes')
      $alert .= '<a href="/bank.php"><img src="/gfx/madesale.png" height="16" width="16" title="An item you were selling has been purchased!" alt="new bank history" class="inlineimage" /></a>';

    // --- check for notable bank transactions -----
    if($user['storeclosed'] == 'yes')
      $alert .= '<a href="/mystore.php"><img src="/gfx/storeclosed.png" height="16" width="16" title="Your store was closed." alt="your store has been closed!" class="inlineimage" /></a>';

    // --- any posts received gold stars? ----------
    if($user['newgoldstar'] == 'yes')
      $alert .= '<a href="/specialposts.php?resident=' . link_safe($user['display']) . '"><img src="/gfx/newgoldstar.png" width="16" height="16" title="A post you made got a gold star sticker!" alt="received gold stars" class="inlineimage" /></a>';

    // --- new comment on your profile? ------------
    if($user['newcomment'] == 'yes')
      $alert .= '<a href="/residentprofile.php?resident=' . link_safe($user['display']) . '#comments"><img src="/gfx/newcomment.png" width="16" height="16" title="Someone has left a comment on your profile!" alt="new profile comment" class="inlineimage" /></a>';

    // --- administrative overbuy reports ----------
    if($user['admin']['clairvoyant'] == 'yes' && $user['admin']['manageaccounts'] == 'yes')
    {
      $data = $database->FetchSingle('SELECT COUNT(userid) AS c FROM psypets_overbuy_report');
      if((int)$data['c'] > 0)
        $alert .= ' <a href="/adminoverbuyreports.php">OB</a>';

      $data = $database->FetchSingle('SELECT COUNT(idnum) AS c FROM psypets_failedlogins');
      if((int)$data['c'] > 0)
        $alert .= ' <a href="/adminloginfailures.php">FL</a>';
    }

    if($user['admin']['alphalevel'] > 0)
    {
      $goal = include 'immediategoal.php';
      $alert .= ' | <b style="color:#906;">' . $goal . '</b>';
    }

    // --- halloween trick-or-treat ----------------
    if($now_month == 10 && $now_day >= 29 && $now_day <= 31)
    {
      if($now >= $user['tot_time'])
        $alert .= ' <a href="/trickortreat.php">*knock, knock, knock*</a>';
    }
    else if($EASTER > 0)
    {
      if($now >= $user['tot_time'])
        $alert .= ' <a href="/easterbunny.php">*knock, knock, knock*</a>';
    }
    else if($now_month >= 5 && $now_month <= 8)
    {
      if($now >= $user['tot_time'])
      {
        $rings = array('ding', 'ling', 'ring');
        $alert .= ' <a href="/icecreamtruck.php">♪' . $rings[array_rand($rings)] . '-a-' . $rings[array_rand($rings)] . '-a-' . $rings[array_rand($rings)] . '♪</a>';
      }
    }
    else if($FBI_QUEST === true)
      $alert .= ' <a href="/myhouse/fbi.php">*knock, knock, knock*</a>';

    // --- display alerts --------------------------
    if(strlen($alert) > 0)
      echo ' | ' . $alert;

    if($maintenance_when >= 2345)
    {
      echo ' | <span class="failure">Maintenance in ' . (2360 - $maintenance_when) . ' minute';
      if($maintenance_when < 2359)
        echo 's';
      echo '</span>';
    }

    // --- broadcasting day? -----------------------
    if(date('D', time()) == $SETTINGS['game_broadcastday'] || $BROADCAST === true)
    {
      $when = mktime(16, 0, 0);
      if($now < $when + 30 * 60)
      {
        if($now > $when)
          echo ' | <a href="/livebroadcast.php">Live Broadcast in about RIGHT NOW!</a>';
        else
          echo ' | <a href="/livebroadcast.php">Live Broadcast in about ' . Duration($when - $now, 2) . '!</a>';
      }
    }
  }

  // min-width fixes a vanishing display issue with IE7 (and 8?)
  echo '</div>',
       '<ul id="wealth" style="min-width:0;">',
       '<li><a href="/bank.php"><span id="moneysonhand">' . $user['money'] . '</span><span class="money" title="moneys">m</span></a></li>';

  if($user['stickers_to_give'] > 0)
    echo '<li>' . $user['stickers_to_give'] . '<img src="/gfx/goldstar.png" alt=" Gold Star stickers" title="Gold Star stickers" width="16" height="16" class="inlineimage" /></li>';

  if($user['rupees'] > 0)
    echo '<li><a href="/mysteriousshop.php">' . $user['rupees'] . '<img src="/gfx/rupees.png" alt=" Rupees" width="16" title="Rupees" height="16" class="inlineimage" /></a></li>';

  if($user['karma'] > 0)
    echo '<li><a href="/wheeloffate.php">' . $user['karma'] . '<img src="/gfx/karma.png" alt=" Karma" title="Karma" width="16" height="16" class="inlineimage" /></a></li>';

  $command = '
    SELECT
      a.amount,
      b.name,
      b.symbol
    FROM
      psypets_group_player_currencies AS a
      LEFT JOIN psypets_group_currencies AS b
    ON a.currencyid=b.idnum
    WHERE
      a.userid=' . $user['idnum'] . '
      AND a.hidden=\'no\'
  ';
  $extra_currencies = fetch_multiple($command, 'fetching extra currencies');

  if(count($extra_currencies) > 0)
  {
    foreach($extra_currencies as $currency)
      echo '<li>' . $currency['amount'] . '<abbr title="' . $currency['name'] . '">' . $currency['symbol'] . '</abbr></li>';
  }

  if($house && array_key_exists('hoursearned', $house))
  {
    $hours_to_allowance = (24 - $house['hoursearned']) % 24;
    if($hours_to_allowance == 0)
      $hours_to_allowance = 24;

    echo '<li><i class="dim">You will collect <a href="/allowance.php">allowance</a> in ' . $hours_to_allowance . ' house hour' . ($hours_to_allowance == 1 ? '' : 's') . '</i></li>';
  }
  
  echo '</ul>';

  require (defined(FRAMEWORK_ROOT) ? FRAMEWORK_ROOT : '') . 'views/_template/menu.php';

  echo '</div>';
}
else
{
  echo '<div id="loggedoutbar">';
  if($NO_LOGIN === true) // enter message about why login has been disabled here:
    echo '<p class="nomargin" style="padding:2px 10px;"><strong class="failure">' . . '</strong></p>';
  else
  {
?>
<div id="login">
 <form method="post">
 <ul class="plainlist logindetails nomargin">
  <li><label for="loginname">Login:</label> <input name="login_name" id="loginname" maxlength="16" style="width:120px;" autofocus speech x-webkit-speech /></li>
  <li><label for="loginpass">Password:</label> <input name="login_password" id="loginpass" type="password" style="width:120px;" speech x-webkit-speech /></li>
 </ul>
<script type="text/javascript">
if(!('autofocus' in document.createElement('input')))
  $('#loginname').focus();
</script>
 <ul class="plainlist nomargin">
  <li><input type="submit" value="Log In" style="width: 70px;" /><?php
		if($SETTINGS['secure_server'])
			echo '<img src="/gfx/roomlock.png" alt="SSL" width="10" height="11" class="inlineimage" />';
?></li>
 </ul>
 </form>
 <div id="signuphelp"><a href="/resetpass.php">Reset lost password</a> <span style="color:white;">&mdash;</span> <a href="/activate.php">Activate new account</a></div>
</div>
<?php
    echo '<div id="signup"><a href="/signup.php" title="Sign Up"><img src="/gfx/signup5.png" alt="Sign Up!" width="600" height="64" /></a></div>';
  }

  echo '</div>';
//  include 'commons/newmenu_loggedout.php';
}
?>
