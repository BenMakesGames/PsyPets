<?php
$require_petload = 'no';
$whereat = 'getbirthday';
$reading_tos = true;

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';

if($user['birthday'] != '0000-00-00')
{
  header('Location: /');
  exit();
}

if($_POST['action'] == 'tellmemoretellmemore')
{
  $year = (int)$_POST['dob_year'];
  $month = (int)$_POST['dob_month'];
  $day = (int)$_POST['dob_day'];

  if($year < 1900 || $day == 0 || checkdate($month, $day, $year) === false)
  {
    $birthday_error = 'This is not a valid date.';
    $errored = true;
  }
  else if(mktime(0, 0, 0, $month, $day, $year) > $now)
  {
    $birthday_error = 'You can\'t have been born in the future... &gt;_&gt;';
    $errored = true;
  }
  else if(($now - mktime(0, 0, 0, $month, $day, $year)) / (60 * 60 * 24 * 365) < 13)
  {
    header('Location: ./logout.php');
    exit();
  }
  else
  {
    $birthdate = $year . '-' . ($month < 10 ? "0$month" : $month) . '-' . ($day < 10 ? "0$day" : $day);

    $command = "UPDATE monster_users SET birthday='$birthdate' WHERE idnum=" . $user["idnum"] . " LIMIT 1";
    $database->FetchNone($command, 'recording birthday');

    header('Location: /');
    exit();
  }
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; Your Date of Birth</title>
<?php include "commons/head.php"; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>Your Date of Birth</h4>
     <p>The <a href="http://www.ftc.gov/ogc/coppa1.htm">Children's Online Privacy Protection Act of 1998</a> tells me that players under the age of 13 may not sign up for PsyPets.  Sorry.</p>
     <p>Please re-enter your birthday:</p>
<?php
if($birthday_error)
  echo "<p style=\"color:red;\">$birthday_error</p>\n";
?>
     <form action="getbirthday.php" method="post">
     <p><select name="dob_month">
      <option value="1"<?= $month == 1 ? " selected" : "" ?>>Jan</option>
      <option value="2"<?= $month == 2 ? " selected" : "" ?>>Feb</option>
      <option value="3"<?= $month == 3 ? " selected" : "" ?>>Mar</option>
      <option value="4"<?= $month == 4 ? " selected" : "" ?>>Apr</option>
      <option value="5"<?= $month == 5 ? " selected" : "" ?>>May</option>
      <option value="6"<?= $month == 6 ? " selected" : "" ?>>Jun</option>
      <option value="7"<?= $month == 7 ? " selected" : "" ?>>Jul</option>
      <option value="8"<?= $month == 8 ? " selected" : "" ?>>Aug</option>
      <option value="9"<?= $month == 9 ? " selected" : "" ?>>Sep</option>
      <option value="10"<?= $month == 10 ? " selected" : "" ?>>Oct</option>
      <option value="11"<?= $month == 11 ? " selected" : "" ?>>Nov</option>
      <option value="12"<?= $month == 12 ? " selected" : "" ?>>Dec</option>
     </select>&nbsp;<input maxlength=2 size=2 name="dob_day" value="<?= $day ?>" />,&nbsp;<input maxlength=4 size=4 name="dob_year" value="<?= $year ?>" /></p>
     <p><input type="hidden" name="action" value="tellmemoretellmemore" /><input type="submit" value="I promise I'm not lying *" style="width:150px;" /></p>
     <p><i>* Because if I am, and I'm actually under 13, my account will be disabled.</i></p>
<?php include "commons/footer_2.php"; ?>
 </body>
</html>
