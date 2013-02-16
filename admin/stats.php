<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($user['admin']['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$recency = (int)$_POST['time'];
$recency_max = (int)$_POST['time2'];

if($recency <= 0)
  $recency = 24;

if($recency_max > $recency)
  $recency_max = $recency - 24;

$time_ago = $now - ($recency * (60 * 60));
$time_ago_max = $now - ($recency_max * (60 * 60));

$search_time = microtime(true);

$command = 'SELECT COUNT(*) AS c FROM monster_users WHERE lastactivity>=' . $time_ago . ' AND lastactivity<=' . $time_ago_max;
$active_data = $database->FetchSingle($command, 'residents recently active');

$command = 'SELECT COUNT(*) AS c FROM monster_users WHERe signupdate>=' . $time_ago . ' AND signupdate<=' . $time_ago_max;
$signup_data = $database->FetchSingle($command, 'residents recently signed up');

$search_time = microtime(true) - $search_time;

$active_count = $active_data['c'];
$signup_count = $signup_data['c'];

$footer_note = 'Took ' . round($search_time, 4) . 's fetching Resident data.';

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Active Residents</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Admin Tools</a> &gt; Active & New Residents</h4>
<?php
if($error_message)
  echo "<p style=\"color:red;\">" . $error_message . "</p>\n";
?>
<form method="post">
<p><input name="time" value="<?= $recency ?>" maxlength="3" size="3" />-<input name="time2" value="<?= $recency_max ?>" maxlength="3" size="3" /> hours ago <input type="submit" value="Update" /></p>
<h5>Within the Last <?= $recency ?> to <?= $recency_max ?> Hours</h5>
<ul>
 <li><?= $active_count ?> residents were active</li>
 <li><?= $signup_count ?> residents signed up</li>
</ul>
</form>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
