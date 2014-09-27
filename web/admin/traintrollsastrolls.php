<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = 'no';

// confirm the session...
require_once "commons/dbconnect.php";
require_once 'commons/threadfunc.php';
require_once 'commons/userlib.php';
require_once 'commons/trolllib.php';
require_once 'commons/sessions.php';

if($admin['abusewatcher'] != 'yes' && $admin['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Plaza Tools &gt; Reset Bayesian Training</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Plaza Tools &gt; Reset Bayesian Training</h4>
<?php
if($_GET['okay'] == 'yes')
{
  $bayesian_filter = new spamchecker();

  $bayesian_filter->resetSpam();


  echo '<p>Resetting...</p>';

  $command = 'SELECT idnum,title,body,createdby FROM monster_posts WHERE troll_flag=\'yes\'';
  $trolls = $database->FetchMultiple($command, 'fetching posts marked as trolls');

  $command = 'SELECT idnum,title,body,createdby FROM monster_posts WHERE troll_flag=\'no\' ORDER BY RAND() LIMIT 50';
  $non_trolls = $database->FetchMultiple($command, 'fetching posts NOT marked as trolls');

  $posts = 0;

  foreach($trolls as $troll)
  {
    $poster = get_user_byid($troll['createdby'], 'user,display');

    $text = $poster['display'] . ' (' . $poster['user'] . ') ' . $troll['title'] . ' ' . $troll['body'];

    $bayesian_filter->train($text, true);

    $posts++;
  }

  echo '<p>Trained filter from ' . $posts . ' posts that were marked as trolling.</p>';

  $posts = 0;

  foreach($non_trolls as $non_troll)
  {
    $poster = get_user_byid($non_troll['createdby'], 'user,display');

    $text = $poster['display'] . ' (' . $poster['user'] . ') ' . $non_troll['title'] . ' ' . $non_troll['body'];

    $bayesian_filter->train($text, false);

    $posts++;
  }

  echo '<p>Trained filter from ' . $posts . ' random posts that were <em>not</em> marked as trolling.</p>';
  
}
else
{
  echo '
    <p>Really-really?</p>
    <ul><li><a href="/admin/traintrollsastrolls.php?okay=yes">Really!</a></li></ul>
  ';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
