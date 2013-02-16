<?php
$_GET['maintenance'] = 'no';

require_once $_SERVER['DOCUMENT_ROOT'] . '/commons/init.php';

require_once 'commons/admincheck.php';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';

if($admin['clairvoyant'] != 'yes')
{
  header("Location: /admin/tools.php");
  exit();
}

if(strlen($_GET['user']) > 0 && $admin['manageaccounts'] == 'yes')
{
  $userfound = get_user_byuser($_GET['user']);
  if($userfound !== false)
  {
    $searched = true;
    $searchby = 'user';

    $_POST['searchby'] = 'user';
    $_POST['user'] = $_GET['user'];
     
    if($_GET['action'] == 'confirm')
      $userfound['activated'] = 'yes';
    else if($_GET['action'] == 'suspect')
      $userfound['activated'] = 'no';
    else if($_GET['action'] == 'enable')
      $userfound['disabled'] = 'no';
    else if($_GET['action'] == 'disable')
      $userfound['disabled'] = 'yes';

    $command = 'UPDATE monster_users SET activated=' . quote_smart($userfound['activated']) . ', ' .
               'disabled=' . quote_smart($userfound['disabled']) . ' WHERE idnum=' . $userfound['idnum'] . ' LIMIT 1';
    $database->FetchNone($command, 'updating resident information');
    
    $users_found[] = $userfound;
  }
}
else if(array_key_exists('search', $_GET))
{
  if($_GET['search'] == 'get')
    $_POST = $_GET;

  $searched = true;

  $searchby = $_POST['searchby'];
  
  if($searchby == '')
    $searchby = 'user';
  
  $searchfor = $_POST[$searchby];

  if($searchby == 'last_ip_address')
    $compare = ' LIKE ' . quote_smart($searchfor);
  else if(strpos($searchfor, ',') !== false && $searchby = 'idnum')
    $compare = ' IN (' . $searchfor . ')';
  else
    $compare = ' LIKE ' . quote_smart($searchfor);

  $command = 'SELECT * FROM monster_users WHERE ' . $searchby . $compare;
  $users_found = $database->FetchMultiple($command, 'fetching matching residents');
}
else
  $searchby = 'user';

require 'commons/html.php';
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
     <h5>Search</h5>
     <form action="/admin/resident.php?search" method="post">
     <table>
      <tr>
       <td><input type="radio" name="searchby" id="search_idnum" value="idnum" <?= $searchby == 'idnum' ? 'checked ' : '' ?>/></td>
       <td>idnum:</td>
       <td><input name="idnum" value="<?= $_POST['idnum'] ?>" onclick="getElementById('search_idnum').checked=true" /></td>
      </tr>
      <tr>
       <td><input type="radio" name="searchby" id="search_user" value="user" <?= $searchby == 'user' ? 'checked ' : '' ?>/></td>
       <td>Login name:</td>
       <td><input name="user" maxlength="32" value="<?= $_POST['user'] ?>" onclick="getElementById('search_user').checked=true" /></td>
      </tr>
      <tr>
       <td><input type="radio" name="searchby" id="search_email" value="email" <?= $searchby == 'email' ? 'checked ' : '' ?>/></td>
       <td>E-mail:</td>
       <td><input name="email" maxlength="64" value="<?= $_POST['email'] ?>" onclick="getElementById('search_email').checked=true" /></td>
      </tr>
      <tr>
       <td><input type="radio" name="searchby" id="search_display" value="display" <?= $searchby == 'display' ? 'checked ' : '' ?>/></td>
       <td>Resident name:</td>
       <td><input name="display" maxlength="32" value="<?= $_POST['display'] ?>" onclick="getElementById('search_display').checked=true" /></td>
      </tr>
      <tr>
       <td><input type="radio" name="searchby" id="search_last_ip_address" value="last_ip_address" <?= $searchby == 'last_ip_address' ? 'checked ' : '' ?>/></td>
       <td>Last known IP:</td>
       <td><input name="last_ip_address" maxlength="32" value="<?= $_POST['last_ip_address'] ?>" onclick="getElementById('search_last_ip_address').checked=true" /></td>
      </tr>
      <tr>
       <td><p>&nbsp;</p></td>
       <td><input type="submit" value="Search" /></td>
      </tr>
     </table>
     </form>
<?php
if($searched)
{
?>
     <h5>Results</h5>
<?php
  if(count($users_found) > 0)
  {
?>
     <table>
      <thead>
       <tr>
        <th>ID#</th>
        <th>Login</th>
        <th>E-mail</th>
        <th>Resident</th>
<?php
    if($user['admin']['manageaccounts'] == 'yes')
    {
      echo '       <th colspan="2">Last Known IP</th>';
      echo "       <th>Status</th>\n";
    }
?>
        <th>Actions</th>
        <th>Notes</th>
       </tr>
      </thead>
      <tbody>
<?php
    $rowclass = begin_row_class();

    foreach($users_found as $userfound)
    {
      $actions = array();

      if($user['admin']['manageaccounts'] == 'yes')
      {
        $actions[] = '
          <form action="/resetpass.php" method="post" name="reset_password" id="reset_password">
          <input type="hidden" name="user_lookup" value="' . htmlentities($userfound['user']) . '" />
          <input type="hidden" name="email_lookup" value="' . htmlentities($userfound['email']) . '" />
          <a href="#" onclick="forms.reset_password.submit(); return false;">issue password reset</a>
          </form>
        ';
      }

      if($user['admin']['possessaccounts'] == 'yes')
      {
        $actions[] = '<a href="/admin/possess.php?user=' . $userfound['idnum'] . '" onclick="return confirm(\'Log in as this user?\');">possess</a>';
        $actions[] = '<a href="/admin/residentwarnings.php?resident=' . link_safe($userfound['display']) . '">warnings</a>';
      }
?>
      <tr class="<?= $rowclass ?>">
       <td valign="top"><?= $userfound['idnum'] ?></td>
       <td valign="top"><?= $userfound['user'] ?></td>
       <td valign="top"><?= $userfound['email'] ?></td>
       <td valign="top"><a href="/residentprofile.php?resident=<?= link_safe($userfound['display']) ?>"><?= $userfound['display'] ?></a></td>
<?php
      if($user['admin']['manageaccounts'] == 'yes')
      {
?>
       <td valign="top"><a href="/loginhistory.php?as=<?= $userfound['idnum'] ?>"><img src="/gfx/petlog_new.png" alt="(view login history)" /></a></td>
       <td valign="top"><?= $userfound['last_ip_address'] ?></td>
       <td valign="top"><ul><?php
        if($userfound["activated"] == "no")
          echo "<li><a href=\"/admin/resident.php?user=" . $userfound["user"] . "&action=confirm\">unconfirmed</a></li>";
        else
          echo "<li><a href=\"/admin/resident.php?user=" . $userfound["user"] . "&action=suspect\">confirmed</a></li>";

        if($userfound["disabled"] == "yes")
          echo "<li><a href=\"/admin/resident.php?user=" . $userfound["user"] . "&action=enable\">disabled</a></li>";
        else
          echo "<li><a href=\"/admin/resident.php?user=" . $userfound["user"] . "&action=disable\">enabled</a></li>";
        
        echo '</ul></td>';

      }

      echo '<td valign="top"><ul><li>' . implode('</li><li>', $actions) . '</li></ul></td>';

      $notes = array();
      if($userfound['activated'] == 'no')
        $notes[] = 'activation key: ' . $userfound['activateid'];
      if($userfound["is_a_bad_person"] == "yes")
        $notes[] = 'GL suspension';

      echo '<td valign="top"><ul><li>' . implode('</li><li>', $notes) . '</li></ul></td>';
?>
      </tr>
<?php
      $rowclass = alt_row_class($rowclass);
    }
?>
      </tbody>
     </table>
<?php
  }
  else
    echo '<p>No resident was found matching that information.</p>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
