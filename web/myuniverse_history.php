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
require_once 'commons/universelib.php';
require_once 'commons/messages.php';

if($user['show_universe'] != 'yes')
{
  header('Location: ./myhouse.php');
  exit();
}

$CONTENT_STYLE = 'background-color: #000;';
$CONTENT_CLASS = 'universe';

$UNIVERSE_MESSAGES = array();

$universe = get_universe($user['idnum']);

if($universe !== false)
{
  $num_pages = get_universe_history_pages($universe['idnum']);
  $page = (int)$_GET['page'];
  if($page < 1 || $page > $num_pages)
    $page = 1;
  
  $events = get_universe_history($universe['idnum'], $page);
}

include 'commons/html.php';
?>
 <head>
  <title><?= $SETTINGS['site_name'] ?> &gt; My Universe</title>
<?php include 'commons/head.php'; ?>
  <link rel="stylesheet" href="styles/multiverse_1.css" />
 </head>
 <body>
<?php include 'commons/header_2.php'; ?>
     <h4><a href="multiverse.php">The Multiverse</a> &gt; <a href="myuniverse.php">My Universe</a> &gt; History</h4>
<?php
if($universe === false || count($events) == 0)
  echo '<p>Your universe has no recorded history!  It\'s possible time doesn\'t even exist yet!  <a href="myuniverse.php">Visit your universe to get things moving...</a></p>';
else
{
  $page_list = paginate($num_pages, $page, 'myuniverse_history.php?page=%s');
  
  echo $page_list;
?>
<table>
 <thead>
  <tr><th>Event</th><th class="centered">When</th></tr>
 </thead>
 <tbody>
<?php
  foreach($events as $event)
  {
    echo '
      <tr>
       <td valign="top">' . $event['event'] . '</td>
       <td valign="top" class="centered">' . duration($now - $event['timestamp'], 2) . ' ago</td>
      </tr>
    ';
  }
?>
 </tbody>
</table>
<?php
  echo $page_list;
}
?>
<?php include 'commons/footer_2.php'; ?>
 </body>
</html>
