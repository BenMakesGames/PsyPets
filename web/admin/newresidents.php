<?php
$IGNORE_MAINTENANCE = true;


require_once 'commons/init.php';

$require_petload = "no";

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($admin['clairvoyant'] != 'yes')
{
  header('Location: /admin/tools.php');
  exit();
}

$max_age = 24;

if($_POST['action'] == 'Search')
{
  $max_age = (int)$_POST['hours'];
  if($max_age < 1)
    $max_age = 1;
  else if($max_age > 168)
    $max_age = 168;
}

$command = 'SELECT idnum,user,display,email,signupdate,last_ip_address,activated,sessionid,lastactivity,timezone FROM monster_users WHERE signupdate>=' . ($now - $max_age * 60 * 60) . ' ORDER BY idnum DESC';
$users_found = $database->FetchMultiple($command, 'fetching recently-signed-up users');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Administrative Tools &gt; Resident Lookup & Tools</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="/admin/tools.php">Administrative Tools</a> &gt; Resident Lookup & Tools</h4>
<?php
 if($error_message)
   echo '<p class="failure">' . $error_message . "</p>\n";
?>
     <form action="adminnewresidents.php" method="post">
     <p>Find accounts up to <input name="hours" maxlength="3" size="3" value="<?= $max_age ?>" /> hours old <input type="submit" name="action" value="Search" /> (up to 1 week - 168 hours)</p>
     </form>
     <h5>Results</h5>
<?php
if(count($users_found) > 0)
{
?>
     <p>Newest accounts are shown first.</p>
     <table>
      <tr class="titlerow">
       <th>ID#</th>
       <th>Login</th>
       <th>E-mail</th>
       <th>Resident</th>
       <th>IP</th>
       <th>Signup</th>
       <th>Activity</th>
       <th>Time Zone</th>
      </tr>
<?php
  $rowclass = begin_row_class();

  foreach($users_found as $userfound)
  {
?>
      <tr class="<?= $rowclass ?>">
       <td><?= $userfound['idnum'] ?></td>
       <td><?= $userfound['user'] ?></td>
       <td><?= $userfound['email'] ?></td>
       <td><a href="residentprofile.php?resident=<?= $userfound['display'] ?>"><?= $userfound['display'] ?></a></td>
       <td><?= $userfound['last_ip_address'] ?></td>
       <td><?= date('M j, H:i', $userfound['signupdate']) ?></td>
       <td><?= $userfound['lastactivity'] == 0 ? '&mdash;' : date('M j, H:i', $userfound['lastactivity']) ?></td>
       <td class="centered"><?= $userfound['timezone'] ?></td>
      </tr>
<?php
    $rowclass = alt_row_class($rowclass);
  }
?>
     </table>
<?php
}
else
  echo '<p>No matching residents were found.</p>';
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
