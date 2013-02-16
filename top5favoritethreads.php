<?php
header('Content-Type: text/html; charset=utf-8');

$require_petload = 'no';
$invisible = 'yes';
$AJAX = true;

require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

$threads = $database->FetchMultiple('SELECT b.idnum,b.title,b.updatedate,b.updateby,b.replies,b.highlight,b.locked,b.updatedate,b.sticky,c.lastread FROM psypets_watchedthreads AS a LEFT JOIN monster_threads AS b ON a.threadid=b.idnum LEFT JOIN monster_watching AS c ON a.threadid=c.threadid WHERE c.user=' . quote_smart($user['user']) .' AND a.userid=' . $user['idnum'] . ' ORDER BY b.updatedate DESC LIMIT 5');

if(count($threads) > 0)
{
  echo '
    <h5>Five Most Recently-updated Favorite Threads</h5>
    <table>
  ';

  $rowclass = begin_row_class();

  foreach($threads as $thread)
  {
    $actions = array();

    $flags = '';

    if($thread['highlight'] > 0)
      $flags .= '<img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/' . $THREAD_HIGHLIGHTS[$thread['highlight']] . '" width="16" height="16" alt="" />';

    if($thread['locked'] == 'yes')
      $flags .= '<img src="/gfx/lock.gif" width="16" height="16" alt="Locked" />';
    else if($thread['updatedate'] < ($now - 6 * 30 * 24 * 60 * 60) && $thread['sticky'] == 'no')
      $flags .= '<img src="/gfx/lock_soft.png" width="16" height="16" alt="Locked (old)" />';

    if($thread['lastread'] < $thread['updatedate'])
    {
      $actions[] = '<a href="/jumptolatestpost.php?threadid=' . $thread['idnum'] . '">first unread post</a>';
      $pre_tag = '<b>';
      $post_tag = '</b>';
    }
    else
    {
      $pre_tag = '';
      $post_tag = '';
    }

    if($thread['replies'] > 19)
      $actions[] = '<a href="/viewthread.php?threadid=' . $thread['idnum'] . '&page=' . ceil(($thread['replies'] + 1) / 20) . '">last page</a>';

    echo '
      <tr class="' . $rowclass . '">
       <td>' . $flags . '</td>
       <td>
        ' . $pre_tag . '<a href="/viewthread.php?threadid=' . $thread['idnum'] . '">' . $thread['title'] . '</a>
      ';

    if(count($actions) > 0)
      echo '<br /><span class="size8">[ ' . implode(', ', $actions) . ' ]</span>';

    echo '
       </td>
      </tr>
    ';

    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>';
}
else
  echo '<p>Up to five threads you subscribe to can be listed here.</p>';
?>
