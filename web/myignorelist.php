<?php

$wiki = 'My_Friends';
$child_safe = false;
$require_petload = 'no';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/sessions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/userlib.php';
require_once 'commons/messages.php';
require_once 'commons/utility.php';

if($_POST['action'] == 'removeignore')
{
  $targetuser = get_user_bydisplay($_POST['resident']);

  if($targetuser !== false)
  {
    remove_enemy($user, $targetuser);

    $CONTENT['messages'][] = '<span class="success">"' . $targetuser['display'] . '" has been removed from your ignore list.</span>';
  }
  else
    $CONTENT['messages'][] = '<span class="failure">There is no resident named "' . $_POST['displayname'] . '"</span>';
}
else if($_POST['action'] == 'ignorebyname')
{
  $targetuser = get_user_bydisplay($_POST['displayname']);

  if($targetuser !== false)
  {
    add_enemy($user, $targetuser);
    remove_friend($user, $targetuser);

    $CONTENT['messages'][] = '<span class="success">"' . $targetuser['display'] . '" has been added to your ignore list.</span>';
  }
  else
    $CONTENT['messages'][] = '<span class="failure">There is no resident named "' . $_POST['displayname'] . '"</span>';
}

$ignore_list = $database->FetchMultiple('
  SELECT b.display
  FROM
    psypets_user_enemies AS a
    LEFT JOIN monster_users AS b
      ON a.enemyid=b.idnum
  WHERE a.userid=' . (int)$user['idnum'] . '
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Ignore List</title>
<?php include 'commons/head.php'; ?>
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>My Ignore List</h5>
		 <ul class="tabbed">
		  <li><a href="/myfriends.php">My Friends</a></li>
			<li class="activetab"><a href="/myignorelist.php">My Ignore List</a></li>
		  <li><a href="/myfollowers.php">My Followers</a></li>
		 </ul>
     <form method="post">
     <p><input name="displayname" maxlength="32" /> <input type="hidden" name="action" value="ignorebyname" /><input type="submit" value="Quick Add" /></p>
     </form>
<?php
if(count($ignore_list) > 0)
{
?>
     <table>
      <thead>
       <tr>
        <th></th>
        <th>Resident</th>
       </tr>
      </thead>
      <tbody>
<?php
  $bgcolor = begin_row_class();

  foreach($ignore_list as $enemy)
  {
?>
     <tr class="<?= $bgcolor ?>">
      <td><form method="post"><input type="image" src="/gfx/rem_ignore.gif" /><input type="hidden" name="action" value="removeignore" /><input type="hidden" name="resident" value="<?= $enemy['display'] ?>" /></form></td>
      <td><?= resident_link($enemy['display']) ?></td>
     </tr>
<?php
    $bgcolor = alt_row_class($bgcolor);
  }
?>
     </table>
<?php
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
