<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/livebroadcastlib.php';

if($user['admin']['alphalevel'] < 6)
{
  header('Location: /livebroadcast.php');
  exit();
}

if($_GET['delete'] > 0)
{
  $id = (int)$_GET['delete'];
  delete_live_broadcast_suggestion($id);
}

$suggestions = get_live_broadcast_suggestions();

if(count($suggestions) > 0)
  $details = get_live_broadcast_suggestion_details();

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Live Broadcasting Suggestion Details</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Live Broadcasting Suggestion Details</h4>
     <p style="color: red; text-align: center;"><strong>Hey, <?= $SETTINGS['author_real_name'] ?>!</strong>  When you talk, <em>sloooooow doooowwwnn</em> so people can understand (and transcribe) you!</p>
     <ul>
      <li><a href="/livebroadcast.php">Go to Live Broadcasting page</a></li>
     </ul>
<?php
$ips_seen = array();

if(count($suggestions) > 0)
{
  $in_category = false;
  $category = '';
  $vote_count = 0;

  foreach($details as $detail)
  {
    if(!$in_category || $category != $detail['vote'])
    {
      if($in_category)
      {
        echo '</table><p>Total votes: ' . $vote_count . '</p>';
        $vote_count = 0;
      }

      $rowclass = begin_row_class();
?>
<h5><?= $detail['vote'] ?></h5>
<table>
 <tr class="titlerow">
  <th></th>
  <th>Display</th>
<?php
      if($user['admin']['manageaccounts'] == 'yes')
      {
?>
  <th>Login</th>
  <th>Email</th>
  <th>Birthday</th>
  <th>IP</th>
<?php
      }
?>
 </tr>
<?php
      $category = $detail['vote'];
      $in_category = true;
    }

    $voter = get_user_byid($detail['residentid'], 'display,user,email,birthday,last_ip_address');
    $vote_count++;
?>
 <tr class="<?= $rowclass ?>">
  <td><a href="/admin/livebroadcasting.php?delete=<?= $detail['residentid'] ?>">X</a></td>
  <td><a href="/residentprofile.php?resident=<?= urlencode($voter['display']) ?>"><?= $voter['display'] ?></a></td>
<?php
    if($user['admin']['manageaccounts'] == 'yes')
    {
?>
  <td><?= $voter['user'] ?></td><td><?= $voter['email'] ?></td><td><?= $voter['birthday'] ?></td><td><?= $voter['last_ip_address'] ?></td>
<?php
      $ips_seen[$voter['last_ip_address']]++;
    }
?>
 </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  if($in_category)
  {
    echo '</table><p>Total votes: ' . $vote_count . '</p>';
    $in_category = false;
  }

  if($user['admin']['manageaccounts'] == 'yes')
  {
    echo '<h5>Repeat IPs</h5>';
    $found_repeats = false;

    foreach($ips_seen as $ip=>$count)
    {
      if($count > 1)
      {
        if($found_repeats === false)
        {
          echo '<ul>';
          $found_repeats = true;
        }

        echo '<li>' . $count . 'x ' . $ip . '</li>';
      }
    }

    if($found_repeats)
      echo '</ul>';
    else
      echo '<p>No repeats found.</p>';
  }
}
else
  echo '<p>No one has made any suggestions for this comic yet.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
