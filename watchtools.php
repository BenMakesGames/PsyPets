<?php
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/threadfunc.php';

$threadid = (int)$_GET['threadid'];

$this_thread = get_thread_byid($threadid);

if($this_thread === false)
{
  header('Location: /plaza.php');
  exit();
}

$this_plaza = get_plaza_byid($this_thread['plaza']);

if($this_plaza === false)
{
  die('error: floating thread!  please report to <a href="/admincontact.php">an administrator</a>.');
}

$watcher_list = explode(',', $this_plaza['admins']);

if(!in_array($user['idnum'], $watcher_list))
{
  header('Location: /viewthread.php?threadid=' . $threadid);
  exit();
}

// old threads not in groups may not be altered except by plaza admins
if($this_thread['updatedate'] < $now - 30 * 24 * 60 * 60 && $this_plaza['groupid'] == 0 && $admin['manageplaza'] != 'yes')
{
  header('Location: /viewthread.php?threadid=' . $threadid . '&msg=160');
  exit();
}

$command = 'SELECT * FROM monster_plaza WHERE groupid=0 ORDER BY `order` ASC';
$plazas = $database->FetchMultipleBy($command, 'idnum');

$command = 'SELECT * FROM monster_watchermove WHERE threadid=' . $threadid . ' LIMIT 1';
$request = $database->FetchSingle($command);

if($_GET['action'] == 'cancelmove' && $request !== false)
{
  $command = 'DELETE FROM monster_watchermove WHERE threadid=' . $threadid . ' LIMIT 1';
  $database->FetchNone($command);

  $gamenote = 'request move: canceled';

  $command = 'INSERT INTO psypets_thread_history (threadid, timestamp, userid, gamenote) VALUES ' .
             '(' . $threadid . ', ' . $now . ', ' . $user['idnum'] . ', ' . quote_smart($gamenote) . ')';
  $database->FetchNone($command);

  $request = false;
}

$command = 'SELECT * FROM psypets_thread_history WHERE threadid=' . $threadid . ' ORDER BY idnum DESC';
$history = $database->FetchMultiple($command, 'fetching thread history');

include 'commons/html.php';
?>
 <head>
<?php include 'commons/head.php'; ?>
  <title><?= $SETTINGS['site_name'] ?> &gt; Plaza Forums &gt; <?= $plazas[$this_thread['plaza']]['title'] ?> &gt; <?= $this_thread['title'] ?> &gt; Watcher Tools</title>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
<?php
if($this_plaza['groupid'] == 0)
{
?>
     <h4><a href="plaza.php">Plaza Forums</a> &gt; <a href="viewplaza.php?plaza=<?= $plazas[$this_thread['plaza']]["idnum"] ?>"><?= $plazas[$this_thread["plaza"]]["title"] ?></a> &gt; <a href="viewthread.php?threadid=<?= $this_thread["idnum"] ?>"><?= format_text($this_thread["title"]) ?></a> &gt; Watcher Tools</h4>
<?php
}
else
{
?>
     <h4><a href="grouppage.php?id=<?= $this_plaza['groupid'] ?>"><?= $this_plaza['title'] ?></a> &gt; <a href="viewplaza.php?plaza=<?= $this_plaza['idnum'] ?>">Forum</a> &gt; <a href="viewthread.php?threadid=<?= $this_thread["idnum"] ?>"><?= format_text($this_thread['title']) ?></a> &gt; Watcher Tools</h4>
<?php
}

if($admin['coder'] == 'yes' && ($this_plaza['idnum'] == 3 || $this_plaza['idnum'] == 4))
  echo '<p class="progress">Do you also need to post to the <a href="/admin/changelog.php">changelog</a>?</p>';
?>
     <h5>Accessibility</h5>
<?php
echo '     <p>This thread IS ';
if($this_thread['sticky'] == 'no')
  echo 'NOT ';
echo 'sticky.  [ <a href="/togglesticky.php?threadid=' . $this_thread['idnum'] . '">Toggle</a> ]</p>';

echo '     <p>This thread IS ';
if($this_thread['locked'] == 'no')
  echo 'NOT ';

echo 'locked.';

if($user['admin']['manageplaza'] == 'yes')
  echo '  [ <a href="/togglelock.php?threadid=' . $this_thread["idnum"] . '">Toggle</a> ]</p>';
?>
     <h5>Highlight Thread</h5>
<?php
if($this_thread['highlight'] == 0 || in_array($this_thread['highlight'], $THREAD_HIGHLIGHTS_ALLOWED))
{
?>
     <p>Want people to notice this thread?  Choose an extra icon to appear next to its name on the plaza.  Stickied threads will not display this icon.</p>
     <form action="/watchhighlight.php?threadid=<?= $this_thread['idnum'] ?>" method="post">
     <table class="nomargin"><tr>
      <td class="centered">None<br /><input type="radio" name="highlight" value="0"<?= $this_thread['highlight'] == 0 ? ' checked' : '' ?> /></td>
<?php
  foreach($THREAD_HIGHLIGHTS_ALLOWED as $index)
    echo '<td class="centered"><img src="//' . $SETTINGS['static_domain'] . '/gfx/forum/' . $THREAD_HIGHLIGHTS[$index] . '" width="16" height="16" alt="" /><br /><input type="radio" name="highlight" value="' . $index . '"' . ($this_thread['highlight'] == $index ? ' checked' : '') . ' /></td>';
?>
     </tr></table>
     <p><input type="hidden" name="action" value="applyhighlight" /><input type="submit" value="Apply" /></p>
     </form>
<?php
}
else
  echo '<p>This thread uses a specialized icon that cannot be changed.</p>';

if($this_plaza['groupid'] == 0)
{
?>
     <h5>Move Thread</h5>
     <p>You may request to have this thread moved, however a watcher from the plaza this thread is to be moved to must approve.</p>
<?php
  if($request === false)
  {
?>
     <form action="/watchrequestmove.php?threadid=<?= $this_thread["idnum"] ?>" method="post">
     <p><select name="destination">
<?php
    foreach($plazas as $plazainfo)
    {
      if($plazainfo['idnum'] != $this_thread['plaza'] && substr($plazainfo['title'], 0, 1) != '#')
      {
?>
       <option value="<?= $plazainfo['idnum'] ?>"><?= $plazainfo['title'] ?></option>
<?php
      }
    }
?>
     </select> <input type="submit" value="Request Move" class="bigbutton" /></p>
     </form>
<?php
  }
  else
    echo '     <p>A request to move this thread to ' . $plazas[$request['destination']]['title'] . ' is already in progress.</p>' .
         '<ul><li><a href="/watchtools.php?threadid=' . $threadid . '&action=cancelmove">Cancel request</a></li></ul>';
}

if(count($history) > 0)
{
  echo '<h5>History</h5>' .
       '<table><tr class="titlerow"><th>Timestamp</th><th>Action</th><th>Resident</th><th>Note</th></tr>';

  $rowclass = begin_row_class();

  foreach($history as $item)
  {
    $action_user = get_user_byid($item['userid'], 'display');
  
    echo '<tr class="' . $rowclass . '">' .
         '<td>' . Duration($now - $item['timestamp'], 2) . ' ago</td><td>' . $item['gamenote'] . '</td>' .
         '<td>' . resident_link($action_user['display']) . '</td><td>' . $item['usernote'] . '</td>' .
         '</tr>';

    $rowclass = alt_row_class($rowclass);
  }
  
  echo '</table>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
