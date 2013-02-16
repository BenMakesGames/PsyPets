<?php
$whereat = 'home';
$wiki = 'Multiverse';
$THIS_ROOM = 'Multiverse';

// confirm the session...
require_once 'commons/dbconnect.php';
require_once 'commons/sessions.php';
require_once 'commons/rpgfunctions.php';
require_once 'commons/grammar.php';
require_once 'commons/formatting.php';
require_once 'commons/inventory.php';
require_once 'commons/universelib.php';
require_once 'commons/messages.php';
require_once 'commons/houselib.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$CONTENT_STYLE = 'background-color: #100;';
$CONTENT_CLASS = 'universe';

$universes = $database->FetchMultiple('
  SELECT b.*,c.display
  FROM psypets_user_friends AS a
    LEFT JOIN psypets_universes AS b
      ON a.friendid=b.ownerid
    LEFT JOIN monster_users AS c
      ON a.friendid=c.idnum
  WHERE
    a.userid=' . (int)$user['idnum'] . '
    AND b.ownerid IS NOT NULL
  ORDER BY c.display ASC
');

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; The Multiverse</title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="//<?= $SETTINGS['static_domain'] ?>/css/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4>The Multiverse</h4>
     <ul>
      <li><a href="myuniverse.php">Go to my universe</a></li>
      <li><a href="myuniverse_history.php">View my universe's history</a></li>
     </ul>
<?php
if(count($universes) > 0)
{
  echo '<h4>Your Friends\' Universes</h4>',
       '<ul>';

  foreach($universes as $universe)
    echo '<li><a href="viewuniverse.php?id=' . $universe['idnum'] . '">'. $universe['display'] . '\'s Universe</a></li>';

  echo '</ul>';
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
