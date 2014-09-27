<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($user['admin']['clairvoyant'] != 'yes' && $user['admin']['manageaccounts'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

if($_POST['submit'] == 'Clear')
{
  $command = 'TRUNCATE TABLE `psypets_failedlogins`';
  $database->FetchNone(($command, 'clearing failed login reports');
  
	// TODO: mail an administrator about this action
  //psymail_user('telkoth', $SETTINGS['site_ingame_mailer'], $user['display'] . ' cleared the failed login logs', $user['display'] . ' cleared the failed login logs');
}

$command = 'SELECT * FROM psypets_failedlogins ORDER BY ip,timestamp DESC';
$attempts = $database->FetchMultiple(($command, 'fetching overbuy reports');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &nbsp; Administrative Tools &nbsp; Failed Login Attempts</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Failed Login Attempts</h4>
<?php
if(count($attempts) > 0)
{
  echo '<table>' .
       '<tr class="titlerow"><th>When</th><th>Login Name</th><th>Login Name Exists</th></tr>';

  foreach($attempts as $attempt)
  {
    if($attempt['ip'] != $oldip)
    {
      echo '<tr style="background-color:#999;"><th colspan="3" class="centered">' . $attempt['ip'] . '<a href="/admin/resident.php?search=get&searchby=last_ip_address&last_ip_address=' . $attempt['ip'] . '"><img src="gfx/search.gif" alt=""></a></th></tr>';

      $oldip = $attempt['ip'];

      $rowclass = begin_row_class();
    }
?>
<tr class="<?= $rowclass ?>">
 <td><?= Duration($now - $attempt['timestamp'], 2) ?> ago</td>
 <td><?= $attempt['username'] ?></td>
 <td class="centered"><?= $attempt['user_exists'] ?></td>
</tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }

  echo '</table>' .
       '<form action="adminloginfailures.php" method="post"><input type="submit" name="submit" value="Clear" /></form>';
}
else
  echo '<p>There are no logged login failure attempts.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
