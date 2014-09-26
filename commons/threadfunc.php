<?php
require_once 'commons/spamchecker.php';

$THREAD_HIGHLIGHTS = array(
  1 => 'anime.gif',
  2 => 'dead.gif',
  3 => 'gasp.gif',
  5 => 'lol.gif',
  6 => 'sweatdrop.png',
  7 => 'suspicious.gif',
  8 => 'wink.gif',
  9 => 'uhhuh.gif',
  10 => 'zzz.gif',
  11 => 'zombie.gif',
  34 => 'rant-and-rage.png',
  4 => 'heart.png',
  12 => 'star.png',
  25 => 'pumpkin.png',
  26 => 'candycane.png',
  23 => 'cherries.png',
  24 => 'pencil.png',
  30 => 'sticky.png',
  27 => 'butterfly.png',
  14 => 'search.png',
  16 => 'arrows.png',
  32 => 'check_white.png',
  17 => 'check_red.png',
  20 => 'exclaim.png',
  31 => 'questionmark.png',
  28 => 'fireball.png',
  18 => 'aplus.png',
  19 => 'wtf.png',
  33 => 'sword.png',
  22 => 'skull.png',
  29 => 'lightbulb.png',
);

$THREAD_SAYS_ALLOWED = array(
  'says',
  'alleges', 'announces', 'articulates', 'asks', 'avers',
  'betokens',
  'complains', 'coos', 'croaks', 'cries',
  'extols', 'expounds',
  'gnarls',
  'hollers',
  'murmurs',
  'opines',
  'postulates', 'predicts', 'proclaims', 'prophesizes',
  'quetches',
  'scoffs', 'shouts', 'suggests', 'squalls',
  'whines', 'whispers',
  'yells',
);

$THREAD_HIGHLIGHTS_ALLOWED = array(
  1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 34, 4, 12, 25, 26, 23, 24, 30, 27, 14, 16, 32, 17,
  20, 31, 28, 18, 19, 33, 22, 29,
);

require_once 'commons/backgrounds.php';

function get_plaza_byid($idnum)
{
  return fetch_single('SELECT * FROM monster_plaza WHERE idnum=' . $idnum . ' LIMIT 1');
}

function get_thread_byid($idnum)
{
  return fetch_single('SELECT * FROM monster_threads WHERE idnum=' . $idnum . ' LIMIT 1');
}

function get_cached_plaza_byid($idnum)
{
  global $_CACHED_PLAZAS;

  if(!array_key_exists($_CACHED_PLAZAS, $idnum))
    $_CACHED_PLAZAS[$idnum] = fetch_single('SELECT * FROM monster_plaza WHERE idnum=' . $idnum . ' LIMIT 1');

  return $_CACHED_PLAZAS[$idnum];
}

function get_cached_thread_byid($idnum)
{
  global $_CACHED_THREADS;
  
  if(!array_key_exists($_CACHED_THREADS, $idnum))
    $_CACHED_THREADS[$idnum] = fetch_single('SELECT * FROM monster_threads WHERE idnum=' . $idnum . ' LIMIT 1');
  
  return $_CACHED_THREADS[$idnum];
}

function get_first_post($threadid)
{
  return fetch_single("SELECT * FROM monster_posts WHERE threadid=$threadid ORDER BY creationdate ASC LIMIT 1");
}

function do_action($action)
{
  if(substr($action, 0, 7) == 'mt_rand')
  {
    $values = explode(',', $action);

    return 'picks a number from between ' . number_format($values[1]) . ' and ' . number_format($values[2]) . ': ' . number_format(mt_rand($values[1], $values[2])) . '.';
  }
  else if($action == 'flipcoin')
    return 'flips a coin: it\'s ' . (mt_rand(0, 1) == 0 ? 'heads' : 'tails') . '!';
  else if($action == 'rps')
  {
    $outcomes = array(0 => 'rock', 1 => 'paper', 2 => 'scissors');
    return 'says "Rock!  Paper!  Scissors!" and picks ' . $outcomes[mt_rand(0, 2)] . '.';
  }
  else if(substr($action, 0, 5) == 'dices')
  {
    $dice = explode(',', $action);

    $total = (int)$dice[3];
    for($x = 0; $x < (int)$dice[1]; ++$x)
      $total += mt_rand(1, (int)$dice[2]);

    return 'rolls ' . $dice[1] . 'd' . $dice[2] . ($dice[3] > 0 ? ('+' . $dice[3]) : '') . ', getting ' . $total . '.';
  }
  else if($action == 'browser')
    return 'is running ' . $_SERVER['HTTP_USER_AGENT'] . '.';
  else if($action == 'randomcharacter')
  {
    require_once 'commons/random_description.php';

    return random_description();
  }
  else
    return '';
}

function create_group_post($threadid, $says, $title, $action, $body, $authorid)
{
  $actiontext = do_action($action);

  fetch_none('INSERT INTO `monster_posts` ' .
             '(`threadid`, `says`, `title`, `action`, `body`, `creationdate`, `updatedate`, `createdby`, `updated`) VALUES ' .
             '(' . $threadid . ', ' . quote_smart($says) . ', ' . quote_smart($title) . ', ' . quote_smart($actiontext) . ', ' . quote_smart($body) . ', ' . time() . ', 0, ' . $authorid . ', \'yes\')');

  return $GLOBALS['database']->InsertID();
}

// creates a new post
function create_post($threadid, $says, $title, $action, $body, $authorid)
{
  $actiontext = do_action($action);

  fetch_none('
    INSERT INTO `monster_posts`
    (
      `threadid`,
      `says`, `title`,
      `action`, `body`,
      `creationdate`, `updatedate`,
      `createdby`,
      `updated`
    )
    VALUES
    (
      ' . $threadid . ',
      ' . quote_smart($says) . ', ' . quote_smart($title) . ',
      ' . quote_smart($actiontext) . ', ' . quote_smart($body) . ',
      ' . time() . ', 0,
      ' . $authorid . ',
      \'yes\'
    )
  ');

  // return the id number of this new post
  return $GLOBALS['database']->InsertID();
}

function get_thread_byidnum($threadid)
{
  return fetch_single("SELECT * FROM monster_threads WHERE idnum=$threadid LIMIT 1");
}

function delete_post_byidnum($postid)
{
  $command = 'SELECT threadid FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
  $post = fetch_single($command, 'fetching post to delete');

  if($post === false)
    return false;

  $report = array();

  $command = 'SELECT plaza FROM monster_threads WHERE idnum=' . $post['threadid'] . ' LIMIT 1';
  $thread = fetch_single($command, 'fetching posts\'s thread');

  $command = 'SELECT idnum,creationdate,createdby FROM monster_posts WHERE threadid=' . $post['threadid'] . ' ORDER BY idnum DESC LIMIT 2';
  $last_posts = fetch_multiple($command, 'fetching last two posts from thread');

  if(count($last_posts) < 2)
  {
    $command = 'DELETE FROM monster_threads WHERE idnum=' . $post['threadid'] . ' LIMIT 1';
    fetch_none($command, 'deleting entire thread as spam');

    $command = 'DELETE FROM monster_watching WHERE threadid=' . $post['threadid'];
    fetch_none($command, 'deleting thread/user last view info');

    $command = 'DELETE FROM monster_reports WHERE threadid=' . $post['threadid'];
    fetch_none($command, 'deleting thread reports');

    $command = 'DELETE FROM psypets_watchedthreads WHERE threadid=' . $post['threadid'];
    fetch_none($command, 'deleting subscriptions to thread');
    
    $report[] = 'deleted thread and related entries';
  }
  else
  {
    if($last_posts[0]['idnum'] == $postid)
    {
      $extra = ',updatedate=' . $last_posts[1]['creationdate'] . ',updateby=' . $last_posts[1]['createdby'];
      $report[] = 'updated thread\'s last post info';
    }
    else
      $extra = '';

    $command = 'UPDATE monster_threads SET replies=replies-1' . $extra . ' WHERE idnum=' . $post['threadid'] . ' LIMIT 1';
    fetch_none($command, 'updating thread to account for removed post');

    $report[] = 'updated thread count';
  }

  $command = 'UPDATE monster_plaza SET replies=replies-1 WHERE idnum=' . $thread['plaza'] . ' LIMIT 1';
  fetch_none($command, 'decrementing number of posts made to the plaza section');
  
  $command = 'DELETE FROM monster_posts WHERE idnum=' . $postid . ' LIMIT 1';
  fetch_none($command, 'deleting spammy post');
  
  $report[] = 'deleted post and updated plaza count';
  
  return $report;
}

function render_post(&$this_post, &$this_thread, &$this_plaza, &$user = array(), $page = 1)
{
  global $POST_BACKGROUNDS, $BADGE_DESC, $SETTINGS;

  $watcher_list = explode(',', $this_plaza['admins']);
  $is_watcher = in_array($user['idnum'], $watcher_list);

  $a_member = (array_search($this_plaza['groupid'], take_apart(',', $user['groups'])) !== false);

  $my_user = get_user_byid($this_post['createdby'], 'is_npc,user,display,graphic,is_a_whale,color,donated,badges,license,openstore');
  $my_user['admin'] = get_admin_byuser($my_user['user']);

  $is_action = (strlen($this_post['action']) > 0);

  $may_edit = ($this_post['createdby'] == $user['idnum'] &&
    $this_post['locked'] == 'no' &&
    $this_thread['locked'] == 'no' &&
    ($this_plaza['groupid'] == 0 || $a_member));

  $may_reply = true;

  if($this_plaza['locked'] == 'yes')
    $may_reply = false;
  else if($this_thread['locked'] == 'yes')
    $may_reply = false;
  else if($this_thread['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $this_thread['sticky'] == 'no' && !$is_watcher)
    $may_reply = false;
  else if($this_plaza['groupid'] != 0 && !$a_member)
    $may_reply = false;
  else if($last_id == $user['idnum'])
    $may_reply = false;

  mt_srand($this_post['idnum']);
  $x = mt_rand(1, 1000);
  $y = mt_rand(1, 1000);

  $post_style = 'width: 100%;';
  if($this_post['background'] > 0)
    $post_style .= ' background: #fff url(\'gfx/postwalls/' . $POST_BACKGROUNDS[$this_post['background']] . '.png\') repeat ' . $x . 'px ' . $y . 'px;';
  else
    $post_style .= ' background-color: #fff;';

  if($this_post['troll_flag'] == 'yes')
  {
    echo '<p class="dim" id="warn' . $this_post['idnum'] . '">Flammable post concealed for your safety.  (<a href="#" onclick="reveal_flames(' . $this_post['idnum'] . '); return false;">Show me; I\'m heat-resistant.</a>)</p>',
         '<div id="troll' . $this_post['idnum'] . '" style="display:none;">';
  }

  $r = hexdec(substr($my_user['color'], 0, 2));
  $g = hexdec(substr($my_user['color'], 2, 2));
  $b = hexdec(substr($my_user['color'], 4, 2));
  $text_color = sqrt(0.241*$r*$r + 0.691*$g*$g + 0.068*$b*$b) < 130 ? '#fff' : '#000';
?>
<div class="plazapost" style="<?= $post_style ?> border-color:#<?= $my_user['color'] ?>;" id="p<?= $this_post['idnum'] ?>">
<div style="background: url('/gfx/residentbar.png') repeat-y;">
 <div class="plazaposttitlebar" style="color:<?= $text_color ?>;background-color:#<?= $my_user['color'] ?>;">
  <div class="plazapostdecoration centered">
<?php
  if($this_thread['createdby'] == $user['idnum'] && $this_thread['opening_post_id'] == $this_post['idnum'])
    echo '<a href="/selftrash_thread.php?threadid=' . $this_thread['idnum'] . '" onclick="return confirm(\'Really?  Trash this tread?  Are you totally OK with that?\');"><img src="gfx/trash.png" width="16" height="16" alt="(trash my thread)" /></a>';

  if($this_post['goldstars'] > 0)
    echo '<img src="/gfx/goldstar.png" width="16" height="16" alt="Gold Star" />&times;' . $this_post['goldstars'];

  if($this_post['createdby'] != $user['idnum'] && $user['stickers_to_give'] > 0)
    echo ' <a href="/giveastar.php?postid=' . $this_post['idnum'] . '"><img src="gfx/goldstar_add.png" width="16" height="16" alt="Give a Star" /></a>';

  if($user['fireworks'] != '' && $this_post['background'] == 0)
    echo ' <a href="#" onclick="firework_popup(' . $this_post['idnum'] . ', \'' . tip_safe($my_user['display']) . '\'); return false;"><img src="gfx/fireworks.png" width="16" height="16" alt="Apply Background" /></a>';
?>
  </div>
  <div class="plazaposttitle"><a href="/residentprofile.php?resident=<?= link_safe($my_user['display']) ?>" style="color:<?= $text_color ?>;"><?= $my_user['display'] ?></a> <?= $this_post['says'] ?>: <?= $this_post['title'] == '' ? '&nbsp;' : format_text($this_post['title']) ?></div>
 </div>
 <div>
  <div class="plazapostresident centered">
<?php
  if(strlen($my_user['display']) > 0)
  {
    echo '<a href="/residentprofile.php?resident=' . link_safe($my_user['display']) . '"><img src="' . user_avatar($my_user) . '" alt="" width="48" height="48" /></a><br />';

    if($my_user['badges'] != '')
    {
      $badges = explode(',', $my_user['badges']);
      foreach($badges as $badge)
        echo '<img src="//' . $SETTINGS['static_domain'] . '/gfx/badges/' . $badge . '.png" title="' . $BADGE_DESC[$badge] . '" alt="' . $BADGE_DESC[$badge] . '" width="20" height="20" /> ';
    }
  }
  else
    echo '<img src="/gfx/shim.gif" width="48" height="48" alt="" /><br />' .
         '<i class="dim">[departed #' . $this_post['createdby'] . ']</i>';

  if($this_post['createdby'] != $user['idnum'] && strlen($my_user['display']) > 0 && $my_user['is_npc'] == 'no')
  {
    echo '<div>';
    
    echo '<a href="/writemail.php?sendto=' . link_safe($my_user['display']) . '"><img src="/gfx/sendmail.gif" width="16" height="16" title="Send Mail" alt="send mail" /></a>';

    if($my_user['license'] == 'yes' && $user['license'] == 'yes')
    {
      echo '<a href="/newtrade.php?user=' . link_safe($my_user['display']) . '"><img src="/gfx/dotrade.gif" width="16" height="16" title="Initiate Trade" alt="start trade" /></a>';
      if($my_user['openstore'] == 'yes')
        echo '<a href="/userstore.php?user=' . link_safe($my_user['display']) . '"><img src="/gfx/forsale.png" width="16" height="16" alt="Visit Store" title="Visit Store" /></a>';
    }

    echo '</div>';
  }
?>
  </div>
  <div class="plazaposttextarea" style="background: transparent;" onclick="togglefireworks(<?= $this_post['idnum'] ?>);">
<?php

  if($this_post['egg'] != 'none' && $this_post['egg'] != 'taken')
  {
    $is_action = true;
    $this_post['action'] .= '<p class="nomargin"><a href="/grabegg.php?id=' . $this_post['idnum'] . '"><img src="//saffron.psypets.net/gfx/items/egg_dyed_' . $this_post['egg'] . '.png" border="0" style="vertical-align:middle;" /> OMG!  You found an egg!  Click it!  Click it!</a></p>';
  }

  if($is_action)
  {
?>
   <div class="rpaction"><?= $this_post['action'] ?></div>
<?php
  }

  if($this_post['troll_flag'] == 'yes')
    echo '<div style="background-color:#fee; border: 1px dashed #c00; padding: 0.5em; margin-bottom: 1em;"><p style="margin-bottom: 0;">Warning: this post may have been written to <em>try</em> to start a fight.  If you <em>must</em> respond to it, please do so in a civil manner.  (If this warning seems to have been applied inappropriately, please <a href="admincontact.php">contact an administrator</a>.)</p></div>';
?>
   <div class="userformatting"><?= format_text($this_post['body']) ?></div>
  </div>
  <div class="plazapostfooter plazapostfooterrow" style="margin-left: 100px;">
<?php
  echo '<span id="postvote' . $this_post['idnum'] . '" style="vertical-align:middle;">';

  if($this_post['voted_on'] == 'no')
  {
    echo
      '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
      '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
    ;
  }
  else
  {
    $vote = get_post_vote($this_post['idnum'], $user['idnum']);

    if($vote === false)
      echo
        '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
    else if($vote['vote'] == -1)
      echo
        '<a href="#" onmouseover="hoveron(\'thumbup' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbup' . $this_post['idnum'] . '\')" onclick="thumbsup(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbup.png" class="transparent_image" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onclick="return false;"><img src="//saffron.psypets.net/gfx/forum/thumbdown.png" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
    else if($vote['vote'] == 1)
      echo
        '<a href="#" onclick="return false;"><img src="//saffron.psypets.net/gfx/forum/thumbup.png" id="thumbup' . $this_post['idnum'] . '" /></a>',
        '<a href="#" onmouseover="hoveron(\'thumbdown' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'thumbdown' . $this_post['idnum'] . '\')" onclick="thumbsdown(' . $this_post['idnum'] . '); return false;"><img src="//saffron.psypets.net/gfx/forum/thumbdown.png" class="transparent_image" id="thumbdown' . $this_post['idnum'] . '" /></a>'
      ;
  }

  echo '</span> <a href="#" onmouseover="hoveron(\'report' . $this_post['idnum'] . '\')" onmouseout="hoveroff(\'report' . $this_post['idnum'] . '\')" onclick="report_post(' . $this_post['idnum'] . ', \'' . tip_safe($my_user['display']) . '\'); return false;" style="vertical-align:middle;"><img src="//saffron.psypets.net/gfx/forum/report.png" width="11" height="16" alt="report post" class="transparent_image" id="report' . $this_post['idnum'] . '" /></a> <i class="dim">Posted ' . local_time($this_post['creationdate'], $user['timezone'], $user['daylightsavings']);

  if($this_post['updatedate'] > $this_post['creationdate'])
    echo '; edited ' . local_time($this_post['updatedate'], $user["timezone"], $user['daylightsavings']);

  $admin_commands = array();

  if($user['admin']['clairvoyant'] == 'yes')
    $admin_commands[] = '<i>#' . $this_post['idnum'] . '</i>';

  if($admin['deletespam'] == 'yes')
    $admin_commands[] = '<a href="/admin/spamcontrol.php?userid=' . $this_post['createdby'] . '&amp;action=delete&amp;p_' . $this_post['idnum'] . '=yes" onclick="return confirm(\'Really delete this post?\');">delete</a>';

  if($user['admin']['alphalevel'] >= 6)
  {
    $text = $user['display'] . ' (' . $user['user'] . ') ' . $this_post['title'] . ' ' . $this_post['body'];
    $bayesian_filter = new spamchecker();

    $admin_commands[] = floor($bayesian_filter->checkSpam($text) * 100) . '% trolly';
  
    if($this_post['troll_flag'] == 'no')
      $admin_commands[] = '<a href="/admin/markastroll.php?postid=' . $this_post['idnum'] . '" onclick="return confirm(\'Flag this post as containing trolls?  BE SURE TO PSYMAIL THE PERSON, and look at their abuse history logs first!\');">trolls</a>';
    else
      $admin_commands[] = '<a href="/admin/unmarkastroll.php?postid=' . $this_post['idnum'] . '">no trolls</a>';

    $admin_commands[] = '<a href="/admin/deletespammypost.php?postid=' . $this_post['idnum'] . '" onclick="return confirm(\'Delete this post?\');">spam</a>';
  }

  if(count($admin_commands) > 0)
    echo ' (' . implode(', ', $admin_commands) . ')';

  echo '</i>';

  if($this_post['createdby'] != $user['idnum'] && $my_user['is_npc'] == 'no')
    echo '<a href="/writemail.php?quotepost=' . $this_post['idnum'] . '" title="Reply via PsyMail"><div class="button smallbutton" style="margin-left:10px;"><div><img src="gfx/sendmail_15.png" class="inlineimage" alt="" /></div></div></a>';

  if($may_edit)
    echo '<a href="/editpost.php?postid=' . $this_post['idnum'] . '&amp;page=' . $page .'"><div class="button"><div>Edit Post</div></div></a>';
  else if($may_reply)
    echo '<a href="/newpost.php?replyto=' . $this_thread['idnum'] . '&amp;quote=' . $this_post['idnum'] . '"><div class="button"><div>Quote</div></div></a>';

  echo '</div></div></div></div>';

  if($this_post['troll_flag'] == 'yes')
    echo '</div>';
}
?>
